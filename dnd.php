<?php

include "functions.php";
include "settings.php";
include "dnd_helpers.php";

$bridge=json_decode(file_get_contents("bridge.json"),true);

$scenes = ["cave"=>["1"=>["r"=>255,"g"=>233, "b"=>158, "bri"=>30, "time"=>50],
                    "2"=>["r"=>202,"g"=>181, "b"=>96, "bri"=>50, "time"=>50],
                    "3"=>["r"=>228,"g"=>205, "b"=>254, "bri"=>150, "time"=>50]],

          "cave_fire"=>["1"=>["r"=>255,"g"=>144, "b"=>0, "bri"=>65, "time"=>50],
                              "2"=>["r"=>255,"g"=>0, "b"=>0, "bri"=>50, "time"=>50],
                              "3"=>["r"=>255,"g"=>66, "b"=>0, "bri"=>150, "time"=>50]],

          "cave_lit"=>["1"=>["r"=>255,"g"=>233, "b"=>158, "bri"=>150, "time"=>50],
                              "2"=>["r"=>202,"g"=>181, "b"=>96, "bri"=>70, "time"=>50],
                              "3"=>["r"=>228,"g"=>205, "b"=>254, "bri"=>200, "time"=>50]],

          "outdoors_day"=>["1"=>["r"=>255,"g"=>255, "b"=>255, "bri"=>254, "time"=>50],
                              "2"=>["r"=>222,"g"=>241, "b"=>255, "bri"=>70, "time"=>50],
                              "3"=>["r"=>222,"g"=>241, "b"=>255, "bri"=>200, "time"=>50]],

          "outdoors_night"=>["1"=>["r"=>255,"g"=>199, "b"=>102, "bri"=>75, "time"=>50],
                              "2"=>["r"=>255,"g"=>255, "b"=>255, "bri"=>10, "time"=>50],
                              "3"=>["r"=>255,"g"=>255, "b"=>255, "bri"=>10, "time"=>50]],

          "green"=>           ["1"=>["r"=>255,"g"=>255, "b"=>163, "bri"=>75, "time"=>50],
                              "2"=>["r"=>6,"g"=>255, "b"=>0, "bri"=>170, "time"=>50],
                              "3"=>["r"=>6,"g"=>225, "b"=>0, "bri"=>170, "time"=>50]],
          ];



$fire = [[233,16,16], [233,98,16], [233,42,16], [220,62,0]];
$ice = [[0,192,220],[0,166,220],[132,210,235]];
$magic = [[153,207,225],[97,190,220],[97,100,220],/*getpurpleyo*/[175,97,220],[97,190,220]];

  if ($_GET["spell"]) {

    echo "<pre>✨ Activating ".$_GET["spell"]." spell! ✨\n";

    if ($_GET["spell"]=="fire") {
      dnd_on();
      dnd_colour_loop($fire, 2, 100);
      dnd_return_to_normal();
    }

    if ($_GET["spell"]=="ice") {
      dnd_on();
      dnd_colour_loop($ice, 5, 100);
      dnd_return_to_normal();
    }

    if ($_GET["spell"]=="magic") {
      dnd_on();
      dnd_colour_loop($magic, 3, 50);
      dnd_return_to_normal();
    }

    if ($_GET["spell"]=="lightning") {
      dnd_on();
      dnd_dim();
      usleep(2000000);
      dnd_bright();
      dnd_flash(255,255,255,254,0);
      dnd_dim();
      usleep(1000000);
      dnd_bright();
      dnd_flash(255,255,255,254,0);
      dnd_dim();
      usleep(500000);
      dnd_flash(255,255,255,254,0);
      dnd_dim();
      dnd_flash(255,255,255,254,0);
      dnd_dim();
      usleep(2000000);
      dnd_return_to_normal();
    }

    echo "</pre>";

  }

  if ($_GET["scene"]) {
    // cave, fire, sun, storm, evil
    echo "<pre>";
    if ($scenes[$_GET["scene"]]) {
      dnd_on();
      dnd_disable_flue();
      dnd_set_normal($_GET["scene"]);
      dnd_set($_GET["scene"]);
    } else {
      echo "that's no scene yo";
    }
    echo "</pre>";
  }

  if((!$_GET["spell"])&&(!$_GET["scene"])) {
    echo "<h2>fluebot in 🐉 d&d mode</h2>";
    echo "<b>waiting for commands!</b>";
  }


    if ($_GET["clear"]=="1") {
      echo "<pre>";
      dnd_set_normal("");
      dnd_enable_flue();
      dnd_return_to_normal();
      echo "<pre>";
    }




 ?>
