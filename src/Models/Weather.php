<?php

namespace Routee\Models;

use Routee\WeatherApi;

class Weather extends Model
{
    /**
     * Searches weather data in the API based on its city id
     *
     * @param string $cityId
     * @return self|null
     */
    public static function find(string $cityId): ?self
    {
        WeatherApi::setCity($cityId);

        $response = WeatherApi::requestWeather();

        if ($response->isSuccessful()) {
            return new self($response->getBody());
        }

        return null;
    }
}
