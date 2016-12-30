<?php

  if (!file_exists("bridge.json")) { echo "bridge.json not found. Run register.php.\n"; exit; }
  $bridge=json_decode(file_get_contents("bridge.json"),true);
  if (!$bridge["username"]) { echo "Run register.php. Username not found.\n"; exit; }

  include "functions.php";

  // Return an output a list of all lights.
  $lights = get($bridge["ip"], $bridge["username"], "lights");

  print_r($lights);

  file_put_contents("lights.json", json_encode($lights, JSON_PRETTY_PRINT));

  echo "\nOutput dumped to lights.json for your convenience.\n";


?>
