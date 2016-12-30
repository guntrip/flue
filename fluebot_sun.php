<?php

function sun($lightId, $sunSetting, $state) {

  global $bridge, $settings;

  echo "  sun mode\n";

  $vars=["hue"=>33582, "xy"=>[0.3476,0.3574]];

  $currentRgb = XYtoRgb($state["xy"][0], $state["xy"][1], $state["bri"]);

  // Are we in daylight savings? :C Also calculates offset for other regions.
  $timezone = new DateTimeZone($settings["timezone"]);
  $server = new DateTimeZone("UTC");
  $dt = new DateTime("now", $timezone);
  $server_dt = new DateTime("now", $server);
  $offset = ($timezone->getOffset($dt) - $server->getOffset($server_dt)) / 3600;

  // Calculate sunset and when to begin.
  $sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $settings["long"], $settings["lat"], $settings["zenneth"], $offset);
  $start = $sunset - ($sunSetting["minutes"]*60);
  $next_sunset = strtotime(date("C",$sunset) . ' +1 day');

  // Calculate sunrise for current day and when to begin that shift
  $sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $settings["long"], $settings["lat"], $settings["zenneth"], $offset);

    // If the sun has risen today, we want the sunrise for *tomorrow*
    $minutes_after_midnight = date("h")*60 + date("i");
    $sunrise_after_midnight = date("h", $sunrise)*60 + date("i", $sunrise);
    if ( ($minutes_after_midnight>$sunrise_after_midnight) && ($minutes_after_midnight<1440) ) {
      $sunrise = strtotime(date("C",$sunrise) . ' +1 day');
    }

  $end = $sunrise - ($sunSetting["sunrise_before"]*60);

  echo "  sunset at ".date("dS H:i", $sunset)." (-".$sunSetting["minutes"]." minutes)\n";
  echo "  next sunrise at ".date("dS H:i", $sunrise)." (-".$sunSetting["sunrise_before"]." minutes)\n";

  if ( (time() > $start) && (time() < $end) ) {
    echo "  ".date("H:i")." - sunset\n";


  } elseif ( (time()>$end) && (time()<$next_sunset) ) {
    echo "  ".date("H:i")." - sunrise\n";


  } else {
    echo "  ".date("H:i")." - day\n";

    $rgb = $sunSetting["day"];

  }

  if (!rgbMatch($rgb, $currentRgb)) {

    echo "  Adjusting to (r:".$rgb["r"].", g:".$rgb["g"].", b:".$rgb["b"].")\n";

    $xy=rgbToXY($rgb);
    $vars = ["xy" => [$xy["x"], $xy["y"]], "bri" => $xy["brightness"]];
    $response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", $vars);

    if (($response[0]["success"])&&($response[1]["success"])) {
      echo "  successful!\n";
    } else {
      echo "  error? Check output below:\n";
      print_r($response);
    }

  } else {
    echo "  Light already set correctly\n";
  }

//  $xy = rgbToXY(50, 255, 98);

  //$rgb =  XYtoRgb($xy["x"] ,$xy["y"] , $xy["brightness"]);


}

?>
