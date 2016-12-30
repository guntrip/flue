# Flue

Flue is a PHP script that can set the RGB or colour temperature of your Phillips Hue lightbulbs depending on the time of day. It provides settings per bulb.

### Installing

Install on a computer on your local network that will always be on. I use my Ubuntu server. You will need PHP installed.

Set up Flue by navigating to the directory and running `php register.php`. This will find your Hue bridge and ask you to press the blue button. Once this is done,
Flue is registered and you can start using it.

You can run Flue with `php fluebot.php`. I recommend setting up a cron job to run it every few minutes.

### Configuration

The settings live in `settings.php`. You can define each of the bulbs you want Flue to fiddle with in the `lights` array:

```php
"1" => [ "nickname" => "Ceiling",
         "mode" => "sun",
         "colour_mode" => "temp",
         //"day" => ["r"=>255, "g"=> 0, "b" => 240],
         //"night" => ["r"=>0, "g"=> 255, "b" =>12],
         "day" => 165,
         "night" => 468,
         "minutes" => 25, // minutes before sunset to begin fade
         "sunrise_before" => 100]
```

The index is the bulb's ID. I've added a little script, `getlights.php`, which will dump a list of all of your bulbs - including their IDs - to `lights.json`. The individual light settings
are explained below:

| Setting        | Description                                                                                                                                        |
|----------------|----------------------------------------------------------------------------------------------------------------------------------------------------|
| nickname       | This is only used for a friendlier debugging experience.                                                                                           |
| mode           | Allows for more modes in future. Currently the only mode is `sun`, adjusting the colours depending on where the sun is.                            |
| colour_mode    | Use `temp` to provide a colour temperature or `rgb`.                                                                                               |
| day            | Colour when the sun is up. Provide either a colour temperature (153 to 500) or, if using rgb, an array such as `["r"=>123, "g"=> 142, "b" => 145]` |
| night          | Colour when the sun is down.                                                                                                                       |
| minutes        | How many minutes the sunset will last, Flue will slowly blend the colours.                                                                         |
| sunrise_before | Number of minutes to bring sunrise forward. If you wake up early, like I do, it's nice to blast yourself with daylight.                            |
| night_link     | If this bulb should only be lit when another is in `night` mode (or shifting to it), reference the bulb's id in an array here: `["light"=>1]`. It will be switched off during daytime hours.     |

There are also some global settings:

| Setting    | Description                                                                                                                                                                                                                      |
|------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| timezone   | Your timezone. Pick from here: http://php.net/manual/en/timezones.php                                                                                                                                                            |
| long       | Longitude of where you are.                                                                                                                                                                                                      |
| lat        | The latitude of where you are.                                                                                                                                                                                                   |
| zenneth    | Angle of the sun at sunset and sunrise. It's safe to leave this as it is.                                                                                                                                                        |
| transition | This defines how long, in multiples of 100ms, each of change should transition for. 50 is 4 seconds and is pleasant. Flue will change bulb colours in steps, each time it is run, over the course of the `minutes` setting but this allows you to specify how jarring those steps should be. |

### Control via a browser

If you also run a web server, you can visit `index.php` for an on/off switch and a "manual" mode.
