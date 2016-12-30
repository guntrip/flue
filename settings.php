<?php

  // use getlights.php to find out the IDs for your light bulbs! :3

  $settings = [ "lights" => [ "1" => [ "nickname" => "Ceiling",
                                       "mode" => "sun",
                                       "colour_mode" => "temp",
                                       /*"day" => ["r"=>255, "g"=> 254, "b" => 226],
                                       "night" => ["r"=>255, "g"=> 176, "b" =>78],*/
                                       "day" => 153,
                                       "night" => 500,
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
                "zenneth" => 90.583333,
                "transition" => 0 // transition time, multiples of 100ms - 50
              ]

?>
