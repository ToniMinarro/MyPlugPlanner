<?php

declare(strict_types=1);

namespace Shared\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpFoundation\Request;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use PcComponentes\OpenApiMessagingContext\Utils\RequestHistory;

final readonly class RestContext implements Context
{
    public function __construct(
        private Kernel $kernel,
        private RequestHistory $requestHistory
    ) {
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario(BeforeScenarioScope $scope): void
    {
        $this->requestHistory->reset();
    }

    /**
     * @When I send a :method request to :uri
     */
    public function sendARequestTo(string $method, string $uri): void
    {
        $this->sendARequestToWithBody($method, $uri);
    }

    /**
     * @When I send a :method request to :uri with body:
     */
    public function sendARequestToWithBody(string $method, string $uri, ?PyStringNode $body = null): void
    {
        $request = Request::create(
            uri: $uri,
            method: $method,
            server: [
                'CONTENT_TYPE' => 'application/json',
            ],
            content: $body?->getRaw(),
        );

        $response = $this->kernel->handle($request);

        $this->kernel->terminate($request, $response);

        $this->requestHistory->add($request, $response);
    }

    /**
     * @When I send a :method request to :uri with query params:
     */
    public function sendARequestToWithQueryParams(string $method, string $uri, PyStringNode $parameters): void
    {
        $parameters = json_decode($parameters->getRaw(), true, 512, JSON_THROW_ON_ERROR);

        $keyValues = [];

        foreach ($parameters as $key => $value) {
            $keyValues[] =  "$key=$value";
        }

        $this->sendARequestTo($method, $uri . '?' . \implode('&', $keyValues));
    }

    /**
     * Checks, that current page response status is equal to specified
     * Example: Then the response status code should be 200
     * Example: And the response status code should be 400
     *
     * @Then /^the response status code should be (?P<code>\d+)$/
     */
    public function assertResponseStatus(int $code): void
    {
        if ($code !== $this->requestHistory->getLastResponse()->getStatusCode()) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @Then /^print last response$/
     */
    public function printLastResponse(): void
    {
        echo $this->requestHistory->getLastResponse()->getContent();
    }

    /**
     * @Then the response should contain:
     */
    public function assertResponseContains(PyStringNode $text): void
    {
        $response = $this->requestHistory->getLastResponse()->getContent();

        $message = \sprintf('The string "%s" was not found anywhere in the HTML response of the current page.', $text);

        if(!\str_contains($response, (string) $text)) {
            throw new \RuntimeException($message);
        }
    }

    /**
     * @Then the JSON response should be:
     */
    public function assertJsonResponse(PyStringNode $expected): void
    {
        $response = $this->requestHistory->getLastResponse()->getContent();
        if (!$response) {
            throw new \RuntimeException('No response received');
        }

        $message = 'The JSON response is not the expected';

        $arrayResponse = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        \ksort($arrayResponse);
        $sortedSerializedResponse = json_encode($arrayResponse, JSON_THROW_ON_ERROR);

        $arrayExpected = json_decode($expected->getRaw(), true, 512, JSON_THROW_ON_ERROR);
        \ksort($arrayExpected);
        $sortedSerializedExpected = json_encode($arrayExpected, JSON_THROW_ON_ERROR);

        if ($sortedSerializedResponse !== $sortedSerializedExpected) {
            throw new \RuntimeException($message);
        }
    }
}
