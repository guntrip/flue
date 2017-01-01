<?php

function check_disable($lights) {

  // Returns a list of lights not to touch!

  global $settings;

  $disable=[];

  foreach ($settings["disable_on"] as $reason => $check) {

    $bulb = $lights[$check["bulb"]];

    if ($bulb["state"]["on"]) {

      if ($check["check"]=="ct") {

        if (($check["check_for"]==$bulb["state"]["ct"])&&($check["check_bri"]==$bulb["state"]["bri"])) {

          echo "!! ".$reason." disable rule met.\n";
          echo "!! skipping ".implode($check["disable"],",")."\n";

          $disable=array_merge($check["disable"], $disable);

        }

      } elseif ( ($check["check"]=="rgb") ) {

        $rgb = XYtoRgb($bulb["state"]["xy"][0], $bulb["state"]["xy"][1], $bulb["state"]["bri"]);

        if (rgbMatch($rgb, $check["check_for"])) {

          echo "!! ".$reason." disable rule met.\n";
          echo "!! skipping ".implode($check["disable"],",")."\n";

          $disable=array_merge($check["disable"], $disable);

        }

      }

    }

  }

  return array_unique($disable);

}

?>
