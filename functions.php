<?php

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

?>
