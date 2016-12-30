<?php

  // use getlights.php to find out the IDs for your light bulbs! :3

  $settings = [ "lights" => [ "1" => [ "nickname" => "Ceiling",
                                       "mode" => "sun",
                                       "day" => ["r"=>255, "g"=> 254, "b" => 226],
                                       "night" => ["r"=>255, "g"=> 234, "b" =>114],
                                       "minutes" => 25, // minutes before sunset to begin fade
                                       "sunrise_before" => 120], // minutes to move sunrise earlier, use to force day mode

                              "2" => [ "nickname" => "tv",
                                       "mode" => "none"],
                              "3" => [ "nickname" => "bloom",
                                       "mode" => "sun_fade"]

                            ],
                "timezone" => "Europe/London",
                "long" => 50.614429,
                "lat" => -2.457621,
                "zenneth" => 90.583333
              ]

?>
