<?php

if (!file_exists("bridge.json")) { echo "bridge.json not found. Run register.php.\n"; exit; }
$bridge=json_decode(file_get_contents("bridge.json"),true);
if (!$bridge["username"]) { echo "Run register.php. Username not found.\n"; exit; }

include "functions.php";
include "settings.php";

// Return an output a list of all lights.
$lights = get($bridge["ip"], $bridge["username"], "lights");

foreach ($lights as $id => $light) {

  if ($settings["lights"][$id]["nickname"]) {
    $nickname = $settings["lights"][$id]["nickname"]." (id: ".$id.")";
  } else {
    $nickname = $light["name"]." (not controlled by Flue, id: ".$id.")";
  }

  echo "# ".$nickname."\n";

  echo "  state: ";
  if ($light["state"]["on"]) {
    echo "on";
  } else {
    echo "off";
  }

  if (!$light["state"]["reachable"]) echo " (unreachable!)";

  echo "\n";

  echo "  type: ".$light["type"]."\n";

  echo "  mode: ".$light["state"]["colormode"]."\n";

  if ($light["state"]["colormode"]=="ct") {

    echo "  temperature: ".$light["state"]["ct"]."\n";

  } elseif ($light["state"]["colormode"]=="xy") {

    $rgb = XYtoRgb($light["state"]["xy"][0], $light["state"]["xy"][1], $light["state"]["bri"]);

    echo "  r: ".$rgb["r"]."\n";
    echo "  g: ".$rgb["g"]."\n";
    echo "  b: ".$rgb["b"]."\n";

  }

  echo "  brightness: ".$light["state"]["bri"]."\n";

  echo "\n";

}

 ?>
