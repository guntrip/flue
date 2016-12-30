<?php

function put($ip, $username, $addr, $vars) {

  $options = array(
    'http' => array(
      'method'  => 'PUT',
      'content' => json_encode( $vars ),
      'header'=>  "Content-Type: application/json\r\n" .
                  "Accept: application/json\r\n"
      )
    );

  $context  = stream_context_create($options);
  $result = file_get_contents("http://".$ip."/api/".$username."/".$addr, false, $context);

  if ($result) return json_decode($result, true);

}

  function post($ip, $vars) {

    $options = array(
      'http' => array(
        'method'  => 'POST',
        'content' => json_encode( $vars ),
        'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
        )
      );

    $context  = stream_context_create($options);
    $result = file_get_contents("http://".$ip."/api", false, $context);

    if ($result) return json_decode($result, true);

  }

  function get($ip, $username, $addr) {
    $result = file_get_contents("http://".$ip."/api/".$username."/".$addr);
    return json_decode($result, true);
  }

  function rgbToXY($rgb) {

    // Following: https://developers.meethue.com/documentation/color-conversions-rgb-xy

    // Convert to 0>1 values
    $colours = ["r"=> ($rgb["r"] / 255), "g" => ($rgb["g"] / 255), "b" => ($rgb["b"] / 255)];

    // Correct for gamma B)
    foreach ($colours as $colour => $v) {
      if ($v > 0.04045) {
        $colours[$colour] = pow( (($v + 0.055) / (1.0 + 0.055)), 2.4 );
      } else {
        $colours[$colour] = $v / 12.92;
      }
    }

    // Convert to xyz
    $x = $colours["r"] * 0.664511 + $colours["g"] * 0.154324 + $colours["b"] * 0.162028;
    $y = $colours["r"] * 0.283881 + $colours["g"] * 0.668433 + $colours["b"] * 0.047685;
    $z = $colours["r"] * 0.000088 + $colours["g"] * 0.072310 + $colours["b"] * 0.986039;

    // Calculate the xy from that
    $result["x"] = $x / ($x + $y + $z);
    $result["y"] = $y / ($x + $y + $z);
    $result["brightness"] = round($y * 255);

    return $result;

  }

  function XYtoRgb($inputX ,$inputY , $brightness) {

    // Following: https://developers.meethue.com/documentation/color-conversions-rgb-xy

    //Calculate xy and z!
    $z = 1.0 - $inputX - $inputY;
    $y = $brightness / 255;
    $x = ($y / $inputY) * $inputX;
    $z = ($y / $inputY) * $z;

    // Convert to rgb
    $colours["r"] = $x * 1.656492 - $y * 0.354851 - $z * 0.255038;
    $colours["g"] = -$x * 0.707196 + $y * 1.655397 + $z * 0.036152;
    $colours["b"] = $x * 0.051713 - $y * 0.121364 + $z * 1.011530;

    // Reverse the gamma correction
    foreach ($colours as $colour => $v) {
      if ($v<=0.0031308) {
        $colours[$colour] = $v * 12.92;
      } else {
        $colours[$colour] = (1.0 + 0.055) * pow($v, (1.0 / 2.4)) - 0.055;
      }

      if ($colours[$colour]>1) $colours[$colour]=1;
      if ($colours[$colour]<0) $colours[$colour]=0;

      $colours[$colour] = round($colours[$colour] * 255);

    }

    return $colours;

  }

  function rgbMatch($a, $b) {
    if ( ($a["r"]==$b["r"]) && ($a["g"]==$b["g"]) && ($a["b"]==$b["b"]) ) {
      return true;
    } else {
      return false;
    }
  }

  function blend($setting, $percentage, $dir) {

    if ($setting["colour_mode"]=="temp") {

      if ($dir=="sunset") {

        $difference = $setting["night"] - $setting["day"];
        return round($setting["day"] + ($difference*$percentage) );

      } else {

        $difference = $setting["night"] - $setting["day"];
        return round($setting["night"] - ($difference*$percentage) );

      }

    } elseif ($setting["colour_mode"]=="rgb") {

      // This could be improved to conserve brightness? It gets dull in the middle :(

      if ($dir=="sunset") { $targetRgb=$setting["night"]; $previousRgb=$setting["day"]; }
      if ($dir=="sunrise") { $targetRgb=$setting["day"]; $previousRgb=$setting["night"]; }

      $rgb["r"] = round($previousRgb["r"] - (($previousRgb["r"] - $targetRgb["r"]) * $percentage));
      $rgb["g"] = round($previousRgb["g"] - (($previousRgb["g"] - $targetRgb["g"]) * $percentage));
      $rgb["b"] = round($previousRgb["b"] - (($previousRgb["b"] - $targetRgb["b"]) * $percentage));

      return $rgb;

    }

  }

?>
