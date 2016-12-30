<?php

if (!file_exists("bridge.json")) { echo "bridge.json not found. Run register.php.\n"; exit; }
$bridge=json_decode(file_get_contents("bridge.json"),true);
if (!$bridge["username"]) { echo "Run register.php. Username not found.\n"; exit; }

include "functions.php";
include "settings.php";
include "fluebot_sun.php";

// Get light status.
$lights = get($bridge["ip"], $bridge["username"], "lights");

// Loop through $settings["lights"]
foreach ($settings["lights"] as $lightId => $light) {

  if ($lights[$lightId]) {

    $state=$lights[$lightId]["state"];
    echo $light["nickname"]." [".$lightId."]: ";

    if (!$state["reachable"]) {
      echo "unreachable\n";
    } else {

      if ($state["on"]) { echo "on"; } else { echo "off"; }
      echo "\n";

      if ($light["mode"]=="sun") { sun($lightId, $light, $state); }

    }

  } else {
    echo "Bridge isn't returning ".$light["nickname"].".";
  }

}

?>
