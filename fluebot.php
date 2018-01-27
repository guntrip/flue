<?php

if (!file_exists("bridge.json")) { echo "bridge.json not found. Run register.php.\n"; exit; }
$bridge=json_decode(file_get_contents("bridge.json"),true);
if (!$bridge["username"]) { echo "Run register.php. Username not found.\n"; exit; }
if (!file_exists("switches.json")) { echo "switches.json not found. Run on.php.\n"; exit; }

$switches=json_decode(file_get_contents("switches.json"),true);

if (!$switches["enabled"]) { echo "Disabled\n"; exit; }

if (!function_exists("rgbToXY")) {
  include "functions.php";
  include "settings.php";
}
  include "fluebot_sun.php";
  include "fluebot_disable.php";
  include "fluebot_nightlight.php";


// Get light status.
$lights = get($bridge["ip"], $bridge["username"], "lights");
$lights_off = [];
$lights_special = [];
$light_modes=[];

if ($_GET["force"]!="1") {
  $disable = check_disable($lights);
  foreach ($disable as $id) { unset($settings["lights"][$id]); }
}

// Loop through $settings["lights"]
foreach ($settings["lights"] as $lightId => $light) {

  if ($lights[$lightId]) {

    $state=$lights[$lightId]["state"];
    echo "# ".$light["nickname"]." [".$lightId."]: ";

    if (!$state["reachable"]) {
      echo "unreachable\n";
      $lights_special[$lightId]=$state;
    } else {

      if ($state["on"]) { echo "on"; } else { echo "off"; }
      echo "\n";

      if ($state["on"]) {

          // Things we do with lights that are on!
          if ($light["mode"]=="sun") { sun($lightId, $light, $state); }
          if ($light["mode"]=="nightlight") { nightlight($lightId, $light, $state); }

          // Is there a night link? If so, we might need to turn it off.
          if (isset($light["night_link"])) {  $lights_special[$lightId] = $state; }

      } else {
         $lights_special[$lightId]=$state;
      }

    }

  } else {
    echo "!! Bridge isn't returning ".$light["nickname"].".";
  }

}

// Check if any lights that have special settings
foreach ($lights_special as $lightId => $state) {

  if (isset($settings["lights"][$lightId]["night_link"])) {

    echo "# Checking night link for ".$settings["lights"][$lightId]["nickname"]." [".$lightId."]\n";
    $linkId = $settings["lights"][$lightId]["night_link"]["light"];

    if ((isset($light_modes[$linkId]))&&($light_modes[$linkId]=="night")&&(!$state["on"])) {

      // Switch on!
      echo "  It's night, switching light on and checking colour.\n";
      //"bri":254
      $response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", ["on"=>true, "transitiontime"=>0, "bri"=>1]);
      //$response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", ["on"=>true, "transitiontime"=>($settings["transition"]*2)]);
      sun($lightId, $settings["lights"][$lightId], $state);

    } elseif ((isset($light_modes[$linkId]))&&($light_modes[$linkId]=="day")&&($state["on"])) {
      //Switch off!
      $response = put($bridge["ip"], $bridge["username"], "lights/".$lightId."/state", ["on"=>false,  "transitiontime"=>($settings["transition"]*2)]);
      echo "  It's day, switching light off.\n";
    }

  }

}

if (sizeof($lights_special)) { nightlight_off($lights_special); }

?>
