<?php

namespace Requester;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Requester\Events\RequestFail;
use Requester\Events\RequestSuccess;

/**
 * Class Request
 * @package Requester
 */
class Request implements RequestInterface
{
    /**
     * @var array
     */
    private $basic = [];

    /**
     * @var array
     */
    private $params = [];

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $channel;

    /**
     * @var string
     */
    protected $errorChannel;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Formatter
     */
    protected $formatter;

    /**
     * @var bool
     */
    protected $trim = true;

    /**
     * Request constructor.
     * @param Response $response
     * @param Formatter $formatter
     */
    public function __construct(Response $response, Formatter $formatter)
    {
        $this->response = $response;
        $this->formatter = $formatter;
    }

    /**
     * @return $this
     */
    public function noTrim()
    {
        $this->trim = false;

        return $this;
    }

    /**
     * @return Request
     */
    public function reset(): self
    {
        return $this
            ->resetBasic()
            ->resetParams()
            ->resetHeaders();
    }

    /**
     * @return $this
     */
    public function resetBasic(): self
    {
        $this->basic = [];

        return $this;
    }

    /**
     * @return $this
     */
    public function resetParams(): self
    {
        $this->params = [];

        return $this;
    }

    /**
     * @return $this
     */
    public function resetHeaders(): self
    {
        $this->headers = [];

        return $this;
    }

    /**
     * @param string $channel
     * @return Request
     */
    public function setChannel(string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @param string $channel
     * @return $this
     */
    public function setErrorChannel(string $channel): self
    {
        $this->errorChannel = $channel;

        return $this;
    }

    /**
     * @param string $url
     * @return Request
     */
    public function setUrl(string $url): self
    {
        $this->client = new Client([
            'verify' => false,
            'base_uri' => $url
        ]);

        return $this;
    }

    /**
     * @param $param
     * @param $value
     * @return $this
     */
    public function addBasic($param, $value): self
    {
        $this->basic[$param] = $value;

        return $this;
    }

    /**
     * @param $param
     * @param $value
     * @return $this
     */
    public function addParam($param, $value): self
    {
        $this->params[$param] = $value;

        return $this;
    }

    /**
     * @param $header
     * @param $value
     * @return $this
     */
    public function addHeader($header, $value): self
    {
        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * @param array $basic
     * @return Request
     */
    public function setBasic(array $basic): self
    {
        $this->basic = $basic;

        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $endPoint
     * @return Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $endPoint): Response
    {
        return $this->send('GET', $endPoint);
    }

    /**
     * @param string $endPoint
     * @return Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(string $endPoint): Response
    {
        return $this->send('POST', $endPoint);
    }

    /**
     * @param string $endPoint
     * @return Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function put(string $endPoint): Response
    {
        return $this->send('PUT', $endPoint);
    }

    /**
     * @param string $endPoint
     * @return Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function patch(string $endPoint): Response
    {
        return $this->send('PATCH', $endPoint);
    }

    /**
     * @param string $endPoint
     * @return Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $endPoint): Response
    {
        return $this->send('DELETE', $endPoint);
    }

    /**
     * @param string $method
     * @param string $endPoint
     * @return Response
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function send(string $method, string $endPoint): Response
    {
        if (empty($this->client)) {
            throw new \Exception('Base url is not specified');
        }

        if ($this->trim) {
            $endPoint = trim($endPoint, '/');
        }

        $data = $this->formatter
            ->setBasic($this->basic)
            ->setParams($this->params)
            ->setHeaders($this->headers)
            ->format($method);

        try {
            $response = $this->client->request($method, $endPoint, $data);

            $result = $this->response->success($response);

            event(new RequestSuccess([
                'method' => $method,
                'end_point' => $endPoint,
                'basic' => $this->basic,
                'params' => $this->params,
                'headers' => $this->headers,
                'response' => $result,
                'channel' => $this->channel,
            ]));
        } catch (RequestException $exception) {
            $result = $this->response->failure($exception);

            event(new RequestFail([
                'method' => $method,
                'end_point' => $endPoint,
                'basic' => $this->basic,
                'params' => $this->params,
                'headers' => $this->headers,
                'response' => $result,
                'channel' => !empty($this->errorChannel) ? $this->errorChannel : $this->channel,
            ]));
        }

        return $result;
    }
}
