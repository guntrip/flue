<?php

function sun($lightId, $sunSetting, $state) {

  global $bridge, $settings, $light_modes, $force_adjust;

  echo "  Sun mode\n";

  $currentRgb = XYtoRgb($state["xy"][0], $state["xy"][1], $state["bri"]);
  //print_r($currentRgb);
  //print_r($state);

  $time=time();
  //$time = strtotime("2017-01-15 17:40"); // for fiddling.

  // Are we in daylight savings? :C Also calculates offset for other regions.
  $timezone = new DateTimeZone($settings["timezone"]);
  $server = new DateTimeZone("UTC");
  $dt = new DateTime("now", $timezone);
  $server_dt = new DateTime("now", $server);
  $offset = ($timezone->getOffset($dt) - $server->getOffset($server_dt)) / 3600;

  // Calculate sunset and sunrise
  $sunset = date_sunset($time, SUNFUNCS_RET_TIMESTAMP, $settings["long"], $settings["lat"], $settings["zenneth"], $offset);
  $sunrise = date_sunrise($time, SUNFUNCS_RET_TIMESTAMP, $settings["long"], $settings["lat"], $settings["zenneth"], $offset);

  echo "  Sunset at ".date("H:i", $sunset)."\n";
  echo "  Sunrise at ".date("H:i", $sunrise)." (-".$sunSetting["sunrise_before"]." minutes - ".date("H:i", ($sunrise-($sunSetting["sunrise_before"]*60))).")\n";
  echo "  Transition time is ".$sunSetting["minutes"]." minutes\n";

  // Convert to minutes after midnight
  $sunset = date("H", $sunset)*60 + date("i", $sunset);
  $sunset_start = $sunset - $sunSetting["minutes"];

  $sunrise = date("H", $sunrise)*60 + date("i", $sunrise) - $sunSetting["sunrise_before"];
  $sunrise_start = $sunrise - $sunSetting["minutes"];

  $now = date("H", $time)*60 + date("i", $time);

  echo "  Current status: ".date("H:i", $time)." - ";
  $mode = "";

  // Figure out the rgb or colour temperature to use.

  if ($now>=$sunset_start) { // After sunset start time.

    if ( $now < $sunset ) { // Sun is setting, blend!

      $percentage = 1-(($sunset-$now)/($sunSetting["minutes"]));
      echo "Sun is setting (".round($percentage*100)."% complete)\n";
      $outcome = blend($sunSetting, $percentage, "sunset");
      $mode="day-night";

    } else { // Sun has set. Simple.

      echo "Night*\n";
      $outcome = $sunSetting["night"];
      $mode="night";

    }

    $light_modes[$lightId]="night";

  } elseif ( ($now>0) && ($now<$sunrise_start) ) { // After midnight, before sunrise.

    echo "Night\n";
    $outcome = $sunSetting["night"];
    $light_modes[$lightId]="night";
    $mode="night";

  } elseif ( ($now>=$sunrise_start) && ($now<$sunset_start) ) { // After sunrise start, before sunset.

    if ($now > $sunrise) { // Sun has risen, simple.

      echo "Day\n";
      $outcome = $sunSetting["day"];
      $mode="day";

    } else { // Sun is rising. Blend!

      $percentage = 1-(($sunrise-$now)/($sunSetting["minutes"]));
      echo "Sun is rising (".$percentage." - ".round($percentage*100)."% complete)\n";
      $outcome = blend($sunSetting, $percentage, "sunrise");
      $mode="day-night";

    }

    $light_modes[$lightId]="day";

  }

  // If someothing has gone very wrong, give up.
  if (!isset($outcome)) {
    echo "  Error: couldn't decide on a colour, skipping.\n";
    return 0;
  }

  // Set colour on bulb.

  if ($sunSetting["colour_mode"]=="rgb") {

      // Calculate current RGB and see if we *need* to change anything
      if ((!rgbMatch($outcome, $currentRgb))|| ($force_adjust)) {

        echo "  Adjusting to (r:".$outcome["r"].", g:".$outcome["g"].", b:".$outcome["b"].")\n";

        // Conver to XY and brightness.
        $xy=rgbToXY($outcome);
        $vars = ["xy" => [$xy["x"], $xy["y"]], "bri" => $xy["brightness"], "transitiontime"=>$settings["transition"]];
        $response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", $vars);

        if (($response[0]["success"])&&($response[1]["success"])&&($response[2]["success"])) {
          echo "  Successful!\n";
        } else {
          echo "  Error? Check output below:\n";
          print_r($response);
        }

      } else {
        echo "  Light already set correctly\n";
      }

  } elseif ($sunSetting["colour_mode"]=="temp") {

    // Do we need to adjust the colour temperature?
    if ( ($outcome != $state["ct"]) || ($force_adjust) ) {

      echo "  Adjusting colour temperature to ".$state["ct"]."\n";

      $vars = ["ct"=>$outcome, "transitiontime"=>$settings["transition"]];

      if ($sunSetting["brightness"]["day"]) {
        if ($mode=="day") $vars["bri"]=$sunSetting["brightness"]["day"];
        if ($mode=="night") $vars["bri"]=$sunSetting["brightness"]["night"];
        if ($mode=="day-night") $vars["bri"]=round($sunSetting["brightness"]["day"] - (($sunSetting["brightness"]["day"] - $sunSetting["brightness"]["night"]) * $percentage));
        if ($mode=="night-day") $vars["bri"]=round($sunSetting["brightness"]["night"] - (($sunSetting["brightness"]["night"] - $sunSetting["brightness"]["day"]) * $percentage));
        echo "  Changing brightness to ".$vars["bri"];
      }

      $response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", $vars);

      if (($response[0]["success"])&&($response[1]["success"])) {
        echo "  Successful!\n";
      } else {
        echo "  Error? Check output below:\n";
        print_r($response);
      }

    } else {
      echo "  Light already set correctly\n";
    }

  }

}

?>
