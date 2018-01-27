<?php

function dnd_on() {
  global $settings, $bridge;
  foreach ($settings["dnd_lights"] as $light) {
  $response = put($bridge["ip"], $bridge["username"], "lights/". $light."/state", ["on"=>true, "transitiontime"=>0]);
  }
}

function dnd_dim() {
  global $settings, $bridge;
  foreach ($settings["dnd_lights"] as $light) {
  $response = put($bridge["ip"], $bridge["username"], "lights/". $light."/state", ["transitiontime"=>0, "bri"=>5]);
  echo "🐉 dimming $light\n";
  }
}
function dnd_bright() {
  global $settings, $bridge;
  foreach ($settings["dnd_lights"] as $light) {
  $response = put($bridge["ip"], $bridge["username"], "lights/". $light."/state", ["transitiontime"=>0, "bri"=>255]);
  echo "🐉 undimming $light\n";
  }
}

function dnd_flash($r,$g,$b,$brightness,$duration) {

  global $settings, $bridge;

  $xy=rgbToXY(["r"=>$r, "g"=>$g, "b"=>$b]);
  $vars = ["xy" => [$xy["x"], $xy["y"]], "bri" => $brightness, "transitiontime"=>$duration];

  foreach ($settings["dnd_lights"] as $light) {
    $response = put($bridge["ip"], $bridge["username"], "lights/".$light."/state", $vars);

    echo "🐉 setting $light to $r, $g, $b\n";

    if ($_GET["show_output"]=="1") {
      if (($response[0]["success"])&&($response[1]["success"])&&($response[2]["success"])) {
        echo "  Successful!\n";
      } else {
        echo "  Error? Check output below:\n";
        print_r($response);
      }
    }

  }

}

function dnd_set($scene) {
  global $settings, $bridge, $scenes;
  foreach ($settings["dnd_lights"] as $light) {
    if ($scenes[$scene][$light]) {
      $thisLight = $scenes[$scene][$light];
      $xy=rgbToXY(["r"=>$thisLight["r"], "g"=>$thisLight["g"], "b"=>$thisLight["b"]]);
      $vars = ["xy" => [$xy["x"], $xy["y"]], "bri" => $thisLight["bri"], "transitiontime"=>$thisLight["time"]];
      $response = put($bridge["ip"], $bridge["username"], "lights/". $light."/state", $vars);
      echo "🐉 setting $light to $scene\n";
    }
  }
}

function dnd_colour_loop($colours, $loops, $nice_delay) {

  // calculate sleeps! $nice_delay in milseconds (1000 = 1sec).
  $hue_delay = ceil($nice_delay / 100); // multiples of 100 ms
  $php_delay = $nice_delay * 1000; // 1000 microseconds in a milisecond

  for ($i=0; $i < $loops; $i++) {
    foreach ($colours as $colour) {
      dnd_flash($colour[0],$colour[1],$colour[2],254,$hue_delay);
      usleep($phpsleep);
    }
  }
}

function dnd_return_to_normal() {
  global $settings, $bridge, $force_adjust;
  $switches = json_decode(file_get_contents("switches.json"),true);
  if ($switches["dnd_scene"]!="") {
    echo "🐉 resetting to ".$switches["dnd_scene"]."!\n\n";
    dnd_set($switches["dnd_scene"]);
  } else {
    echo "🐉 resetting to normality!\n\n";
  $force_adjust=true;
  include "fluebot.php";
  }
}

function dnd_disable_flue() {
  // stop fluebot from interfering
  $switches = json_decode(file_get_contents("switches.json"),true);
  $switches["enabled"]=false;
  file_put_contents("switches.json", json_encode($switches));
}

function dnd_enable_flue() {
  // stop fluebot from interfering
  $switches = json_decode(file_get_contents("switches.json"),true);
  $switches["enabled"]=true;
  file_put_contents("switches.json", json_encode($switches));
}

function dnd_set_normal($scene) {
  $switches = json_decode(file_get_contents("switches.json"),true);
  $switches["dnd_scene"]=$scene;
  file_put_contents("switches.json", json_encode($switches));
}

?>
