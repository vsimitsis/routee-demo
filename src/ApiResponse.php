<?php

namespace Routee;

use GuzzleHttp\Psr7\Response;

class ApiResponse
{
    /** @var Response $rawResponse */
    private $rawResponse;

    /**
     * Constructor for the response
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->rawResponse = $response;
    }

    /**
     * Returns raw response if set
     *
     * @return Response|null
     */
    public function getRaw(): ?Response
    {
        return $this->rawResponse;
    }

    /**
     * Check if the response is successful
     *
     * @return boolean
     */
    public function isSuccessful(): bool
    {
        return $this->getRaw()->getStatusCode() === 200;
    }

    /**
     * Get the body of the response
     *
     * @param boolean $asArray
     * @return stdClass|array|null
     */
    public function getBody(bool $asArray = false)
    {
        return json_decode($this->getRaw()->getBody(), $asArray) ?? null;
    }
}
