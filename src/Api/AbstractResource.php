<?php

namespace Olssonm\Swish\Api;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request as Psr7Request;
use Olssonm\Swish\Exceptions\ClientException;
use Olssonm\Swish\Exceptions\ServerException;
use Olssonm\Swish\Exceptions\ValidationException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractResource
{
    protected ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve resource
     */
    abstract public function get($transaction);

    /**
     * Create resource
     */
    abstract public function create($transaction);

    /**
     * Cancel transaction
     */
    abstract public function cancel($transaction);

    /**
     * Main API caller
     *
     * @param string $verb
     * @param string $uri
     * @param array<string, string> $headers
     * @param string|null $payload
     * @return ResponseInterface
     * @throws ClientException|ServerException|ValidationException
     */
    protected function request(string $verb, string $uri, array $headers = [], string|null $payload = null) : ResponseInterface
    {
        $request = new Psr7Request(
            $verb,
            $uri,
            array_merge([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ], $headers),
            $payload
        );

        $response = $this->client->send($request);

        $status = $response->getStatusCode();
        $level = (int) \floor($status / 100);

        switch (true) {
            case $status == 403:
                // No break
            case $status == 422:
                $this->triggerException(
                    ValidationException::class,
                    'Validation error',
                    $request,
                    $response
                );
                // No break
            case $level == 4:
                $this->triggerException(
                    ClientException::class,
                    'Client error',
                    $request,
                    $response
                );
                // No break
            case $level == 5:
                $this->triggerException(
                    ServerException::class,
                    'Server error',
                    $request,
                    $response
                );
        }

        return $response;
    }

    /**
     * Trigger a request exception
     *
     * @param string $class
     * @param string $label
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return void
     */
    protected function triggerException(
        string $class,
        string $label,
        RequestInterface $request,
        ResponseInterface $response
    ): void {
        $message = \sprintf(
            '%s: `%s %s` resulted in a `%s %s` response',
            $label,
            $request->getMethod(),
            $request->getUri()->__toString(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        );

        /**
         * @var \Exception
         */
        throw new $class(
            $message,
            $request,
            $response
        );
    }
}
