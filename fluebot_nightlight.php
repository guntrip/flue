<?php

function nightlight($lightId, $sunSetting, $state) {

  global $switches, $bridge, $settings;

  // Stores progress in switches.json -
  // nightlight => lightid => started
  //                          bri
  //                          active

  // What is the current time in minutes after midnight?
  $time = time();
  //$time = strtotime("2017-01-16 23:58"); // for fiddling.
  $timestamp=$time;
  $minutesAfterMidnight=date("H", $time)*60 + date("i", $time);

  // Are we allowed to do things?
  if ( (($sunSetting["after_midnight"]) && (($minutesAfterMidnight>$sunSetting["start"]) || ( ($minutesAfterMidnight>=0) && ($minutesAfterMidnight<$sunSetting["end"]) ) )) ||
       ((!$sunSetting["after_midnight"]) && ($minutesAfterMidnight>$sunSetting["start"]) && ($minutesAfterMidnight<$sunSetting["end"]) ) ) {

    echo "  Nightlight (".$minutesAfterMidnight.")\n";

    // Set up.
    if ($switches["nightlight"][$lightId]["active"]) {

      // Has anything changed?
      if ($state["bri"]!=$switches["nightlight"][$lightId]["bri"]) {
        echo " Someone has changed something! Stopping.\n";
        return 0;
      }

      // Figure out percentage we need to dim.
      $length = round( ($timestamp - $switches["nightlight"][$lightId]["started"]) / 60 );

      if ($length==0) { echo "  No time has past. Skipping\n"; return 0; }

      $percentage = 1 - ($length / $sunSetting["duration"]);
      echo "  Dimmed: ".round($percentage*100)."% (".$length." out of ".$sunSetting["duration"]." minutes)\n";

    } else {

      // Have they /just/ been switched off?
      if ( $switches["nightlight"][$lightId]["switchedOffAt"] ) {
        $gap = $switches["nightlight"][$lightId]["switchedOffAt"] - $timestamp;
        if ($gap < 45) {
          echo "  Switched off $gap seconds ago.\n";
          return 0;
        }
      }

      // Start
      $switches["nightlight"]=[$lightId => ["active"=>true, "started"=>$timestamp]];
      $percentage=1;
      echo "  Starting!! Progress: 100%\n";

    }

    if ($percentage<=0) {

        // Switch off!
        $response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", ["on"=>false,  "transitiontime"=>($settings["transition"]*2)]);
        $switches["nightlight"][$lightId]["active"]=false;
        $switches["nightlight"][$lightId]["switchedOffAt"]=$timestamp;
        file_put_contents("switches.json", json_encode($switches));
        echo "  All done, switching off :)\n";

    } else {

        // Calculate brightness
        $brightness = round($sunSetting["starting_brightness"] * $percentage);

        if ($brightness==$switches["nightlight"][$lightId]["bri"]) {
          echo "  Brightness hasn't changed yet.\n";
          return 0;
        }

        // Send brightness to bulb!
        $vars = ["bri" => $brightness, "transitiontime"=>$settings["transition"]];
        if ($sunSetting["ct"]) $vars["ct"] = $sunSetting["ct"];

        echo "  Adjusting brightness to ".$brightness.".\n";

        $response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", $vars);

        if (($response[0]["success"])&&($response[1]["success"])) {

          echo "  Successful! Saving progress to switches.json\n";

          $switches["nightlight"][$lightId]["bri"]=$brightness;
          file_put_contents("switches.json", json_encode($switches));

        } else {
          echo "  Error? Check output below:\n";
          print_r($response);
        }

    }

  } else {
    // Nothing to do!
  }

}

function nightlight_off($off) {
  global $switches;
  // Are any of the switched off lights part of of a ongoing nightlight?
  $changed=false;

  foreach ($off as $id => $state) {
    if (($switches["nightlight"][$id])) {
      if (($switches["nightlight"][$id]["active"])) {
      $switches["nightlight"][$id]["active"]=false;
      $changed=true;
      echo "  Light $id was part of a nightlight and has been switched off. Adjusting switches.json.\n";
      }
    }
  }
  if ($changed) file_put_contents("switches.json", json_encode($switches));
}

 ?>
