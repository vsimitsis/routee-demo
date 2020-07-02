<?php

namespace Routee;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class WeatherApi
{
    /** @var Client $client */
    protected static $client;

    /** @var int $cityId */
    protected static $cityId;

    /**
     * Set the city id for weather stats
     * 
     * @param string $cityId
     */
    public static function setCity(string $cityId)
    {
        self::$cityId = $cityId;
    }

    /**
     * Get the city id for weather stats
     * 
     * @return string 
     */
    public static function getCity(): string
    {
        return self::$cityId;
    }

    /**
     * Return the api key for open weather
     * 
     * @return string
     */
    protected static function getApiKey(): string
    {
        return Config::get('api.openweather.key');
    }

    /**
     * Sends a request to the open weather API with authentication token
     *
     * @param string $method
     * @param string $path
     * @param array $details
     * @throws \Exception
     */
    public static function requestWeather(array $details = [])
    {
        try {
            return new ApiResponse(
                self::client()->get(
                    self::getApiUrl(),
                    self::mergeDefaultOptions($details)
                )
            );
        } catch (ClientException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Gets the Client instance, and if not set, instantiates new one
     *
     * @return Client
     */
    protected static function client(): Client
    {
        if (!self::$client) {
            self::$client = new Client;
        }
        return self::$client;
    }

    /**
     * Return the API
     *
     * @return string
     */
    public static function getApiUrl()
    {
        return sprintf(Config::get('api.openweather.endpoint'), self::getCity(), self::getApiKey());
    }

    /**
     * Returns default headers
     *
     * @return array
     */
    protected static function getDefaultHeaders(): array
    {
        return [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ];
    }

    /**
     * Merges default options for the http request with custom ones
     *
     * @param array $options
     * @return array
     */
    private static function mergeDefaultOptions(array $options): array
    {
        return array_merge_recursive(
            self::getDefaultHeaders(),
            $options
        );
    }
}
