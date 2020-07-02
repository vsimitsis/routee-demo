<?php

require_once __DIR__ . '/vendor/autoload.php';

use Routee\Config;
use Routee\Models\Weather;
use Routee\RouteeApi;

// //In case we want to check other cities too.
$cityID = Config::get('cityId');

//Get Authorized at Routee API
RouteeApi::auth();

$weather = Weather::find($cityID);

if (!$weather) {
    throw new Exception(sprintf('City with ID: %s has not been found.', $cityID));
}

if ($weather->main->temp > Config::get('temp.threshold')) {
    RouteeApi::sendSMS([
        'body' => "Evangelos Simitsis, the temperature is more than 20C. Actual temperature: " . $weather->main->temp,
        'to'   => Config::get('recipient'),
        'from' => 'Routee'
    ]);
} else {
    RouteeApi::sendSMS([
        'body' => "Evangelos Simitsis, the temperature is less than 20C. Actual Temperature: " . $weather->main->temp,
        'to'   => Config::get('recipient'),
        'from' => 'Routee'
    ]);
}

/**
 * config.json is supposed to be excluded from the repo but for obvious reasons is included
 */

/**
 * If i didn't mind to limit the script on browsers only i could implement this
 * by sending a simple ajax call every 10min. Since it's not clear the "Repeat the above procedure every 10 minutes."
 * i would guess someone could `sudo nano /etc/crontab` and add the following line :) 
 */

//  */10 * * * * php projectPath/index.php > /dev/null 2>&1
