<?php

function sun($lightId, $sunSetting, $state) {

  global $bridge, $settings;

  echo "  Sun mode\n";

  $time=time();
  $time = strtotime("2016-12-31 07:50");

  $vars=["hue"=>33582, "xy"=>[0.3476,0.3574]];

  $currentRgb = XYtoRgb($state["xy"][0], $state["xy"][1], $state["bri"]);

  // Are we in daylight savings? :C Also calculates offset for other regions.
  $timezone = new DateTimeZone($settings["timezone"]);
  $server = new DateTimeZone("UTC");
  $dt = new DateTime("now", $timezone);
  $server_dt = new DateTime("now", $server);
  $offset = ($timezone->getOffset($dt) - $server->getOffset($server_dt)) / 3600;

  // Calculate sunset and when to begin.
  $sunset = date_sunset($time, SUNFUNCS_RET_TIMESTAMP, $settings["long"], $settings["lat"], $settings["zenneth"], $offset);
  $start = $sunset - ($sunSetting["minutes"]*60);
  $next_sunset = $sunset + 86400;

  // Calculate sunrise for current day and when to begin that shift
  $sunrise = date_sunrise($time, SUNFUNCS_RET_TIMESTAMP, $settings["long"], $settings["lat"], $settings["zenneth"], $offset);

    // If the sun has risen today, we want the sunrise for *tomorrow*
    $minutes_after_midnight = date("h")*60 + date("i");
    $sunrise_after_midnight = date("h", $sunrise)*60 + date("i", $sunrise);
    if ( ($minutes_after_midnight>$sunrise_after_midnight) && ($minutes_after_midnight<1440) ) {
      $sunrise = $sunrise + 86400;
    }

  $end = $sunrise - ($sunSetting["sunrise_before"]*60);

  echo "  Sunset at ".date("dS H:i", $sunset)." (-".$sunSetting["minutes"]." minutes)\n";
  echo "  Next sunrise at ".date("dS H:i", $sunrise)." (-".$sunSetting["sunrise_before"]." minutes)\n";
  echo "  Next sunset at ".date("dS H:i", $next_sunset)."\n";
  echo "  Current time: ".date("H:i", $time)." - ";

  if ( ($time > $start) && ($time < $end) ) {

    if ( $time < $sunset ) {

      // Sun is setting, blend!
      $percentage = 1-(($sunset-$time)/($sunSetting["minutes"]*60));
      echo "Sun is setting (".round($percentage*100)."% complete)\n";

      $outcome = blend($sunSetting, $percentage, "sunset");

    } else {

      // Sun has set. Simple.
      echo "Night\n";
      $outcome = $sunSetting["night"];

    }


  } elseif ( ($time>$end) && ($time<$next_sunset) ) {


    if ($time > $sunrise) {

      // Sun has risen, simple.
      echo "Day\n";
      $outcome = $sunSetting["day"];

    } else {

      // Sun is rising!
      $percentage = 1-(($sunrise-$time)/($sunSetting["sunrise_before"]*60));
      echo "Sun is rising (".round($percentage*100)."% complete)\n";

      //$outcome = blend($sunSetting, $percentage, "sunset");

    }


  } else {
    echo "Day\n";

    $outcome = $sunSetting["day"];

  }

  if (!isset($outcome)) {
    echo "  Error: couldn't decide on a colour, skipping.\n";
    return 0;
  }

  if ($sunSetting["colour_mode"]=="rgb") {

      if (!rgbMatch($outcome, $currentRgb)) {

        echo "  Adjusting to (r:".$outcome["r"].", g:".$outcome["g"].", b:".$outcome["b"].")\n";

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

    if ( $outcome != $state["ct"] ) {

      echo "  Adjusting colour temperature to ".$state["ct"]."\n";

      $vars = ["ct"=>$outcome, "transitiontime"=>$settings["transition"]];
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

//  $xy = rgbToXY(50, 255, 98);

  //$rgb =  XYtoRgb($xy["x"] ,$xy["y"] , $xy["brightness"]);


}

?>
