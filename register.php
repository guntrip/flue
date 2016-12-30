<?php

set_time_limit(0);

include "functions.php";

// Note: this is going to get confused if you have more than one bridge.

// Use the Hue "broker server discover process" to get the bridge details:
$bridges = json_decode(file_get_contents("http://www.meethue.com/api/nupnp"), true);
$bridge = ["ip"=>$bridges[0]["internalipaddress"], "id"=>$bridges[0]["id"]] ;

if (!$bridge["ip"]) {

  echo "No bridge found.\n";
  exit;

}

echo "Hue Bridge found! ".$bridge["id"]." on ".$bridge["ip"].".\n";
echo "Please press the big blue button.\n";

// Say hello to the bridge.
$username = false;
$tries = 0;

while ((!$username) && ($tries<10)) {

  $response = post($bridge["ip"],["devicetype"=>"flue#bertie"]);

  if ((isset($response[0]["error"]))&&($response[0]["error"]["description"]=="link button not pressed")) {

    $tries++;
    sleep(2);

  } elseif ($response[0]["success"]["username"]) {

    $username = $response[0]["success"]["username"];

  } else {

    $tries++;
    sleep(2);

  }

}

if ($username) {

  echo "Thanks.\n";
  echo "API username received! Hello ".$username.".\n";

  // Save bridge details
  file_put_contents("bridge.json", json_encode(["ip"=>$bridge["ip"], "id"=>$bridge["id"], "username"=>$username]));

  echo "Details saved to bridge.json.\n";
  echo "You're all set!\n";

} else {

  echo "Gave up waiting :(\n";

}


 ?>
