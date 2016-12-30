<?php

  // use getlights.php to find out the IDs for your light bulbs! :3

  $settings = [ "lights" => [ "1" => [ "nickname" => "Ceiling",
                                       "mode" => "sun",
                                       "colour_mode" => "temp",
                                       //"day" => ["r"=>255, "g"=> 0, "b" => 240],
                                       //"night" => ["r"=>0, "g"=> 255, "b" =>12],
                                       "day" => 153,
                                       "night" => 500,
                                       "minutes" => 25, // minutes before sunset to begin fade
                                       "sunrise_before" => 100], // minutes to move sunrise earlier, use to force day mode

                              "2" => [ "nickname" => "tv",
                                       "mode" => "none"],
                              "3" => [ "nickname" => "bloom",
                                       "mode" => "sun_fade"]

                            ],
                "timezone" => "Europe/London",
                "long" => 50.614429,
                "lat" => -2.457621,
                "zenneth" => 90.583333,
                "transition" => 0 // transition time, multiples of 100ms - 50
              ]

?>
