<?php

namespace Routee;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class RouteeApi
{
    /** @var Client $client */
    protected static $client;

    /** @var Client $client */
    protected static $authToken;

    /**
     * Authorize at Routee Api
     * 
     * @throws ClientException
     * @throws Exception
     */
    public static function auth()
    {
        if (!empty($authToken)) {
            return;
        }

        try {
            $response =  new ApiResponse(
                self::client()->post(
                    'https://auth.routee.net/oauth/token',
                    self::getAuthHeaders()
                )
            );
        } catch (ClientException $e) {
            throw new Exception($e->getMessage());
        }

        if (!$response->isSuccessful()) {
            throw new Exception('Something went wrong. Please try again later.');
        }

        self::$authToken = $response->getBody()->access_token;
    }

    /**
     * Sends an SMS
     * 
     * @return ApiResponse
     */
    public static function sendSMS(array $data): ApiResponse
    {
        return self::request('POST', Config::get('api.routee.sms-endpoint'), ['body' => json_encode($data)]);
    }

    /**
     * Sends a request to the Routee API with authentication token
     *
     * @param string $method
     * @param string $path
     * @param array $details
     * @return ApiResponse
     * @throws \Exception
     */
    protected static function request(string $method, string $path, array $details = []): ApiResponse
    {
        if (!self::$authToken) {
            throw new Exception('No auth token is set.');
        }
        
        // Convert HTTP method to the class method
        $method = strtolower($method);

        // Make a call
        try {
            return new ApiResponse(
                self::client()->$method(
                    $path,
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
    private static function client(): Client
    {
        if (!self::$client) {
            self::$client = new Client();
        }
        return self::$client;
    }

    /**
     * Encodes the routee app id and key for authorization request
     * 
     * @return string
     */
    protected static function encodeKey(): string
    {
        $keyString = Config::get('api.routee.id') . ':' . Config::get('api.routee.key');

        return base64_encode($keyString);
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
                'Authorization' => sprintf('Bearer %s', self::$authToken),
                'Accept' => 'application/json',
                'content-type' => "application/json"
            ]
        ];
    }

    /**
     * Return the auth headers
     * 
     * @return array
     */
    protected static function getAuthHeaders(): array
    {
        return [
            'headers' => [
                'authorization' => sprintf('Basic %s', self::encodeKey()),
                'content-type' => "application/x-www-form-urlencoded"
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ];
    }

    /**
     * Merges default options for the http request with custom ones
     *
     * @param array $options
     * @return array
     */
    protected static function mergeDefaultOptions(array $options): array
    {
        return array_merge_recursive(
            self::getDefaultHeaders(),
            $options
        );
    }
}
