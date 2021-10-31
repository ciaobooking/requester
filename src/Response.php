<?php

namespace Requester;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * Class Response
 * @package Requester
 */
class Response implements ResponseInterface
{
    /**
     * @var PsrResponseInterface
     */
    private $response;

    /**
     * @var RequestException
     */
    private $exception;

    /**
     * @var bool
     */
    private $success = false;

    /**
     * @var bool
     */
    private $failure = false;

    /**
     * @var bool
     */
    private $processed = false;

    /**
     * @var array
     */
    private $appends = [];

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return $this->failure;
    }

    /**
     * @param $param
     * @param $value
     * @return $this
     */
    public function append(string $param, $value): self
    {
        $this->appends[$param] = $value;

        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        if (!$this->processed) {
            return null;
        }

        return array_merge(
            $this->appends,
            $this->formatSuccess()
        );
    }

    /**
     * @return array
     */
    public function errors()
    {
        if (!$this->processed) {
            return null;
        }

        return $this->formatFailure();
    }

    /**
     * @return $this
     */
    public function resetInstance(): self
    {
        $this->success = false;
        $this->failure = false;
        $this->processed = false;

        return $this;
    }

    /**
     * @param PsrResponseInterface $response
     * @return $this
     */
    public function success(PsrResponseInterface $response): self
    {
        $this->resetInstance();

        $this->success = true;
        $this->response = $response;
        $this->processed = true;

        return $this;
    }

    /**
     * @param RequestException $exception
     * @return $this
     */
    public function failure(RequestException $exception): self
    {
        $this->resetInstance();

        $this->failure = true;
        $this->exception = $exception;
        $this->processed = true;

        return $this;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function formatSuccess()
    {
        $body = $this->response->getBody();

        $result = json_decode($body, true);
        $result = is_array($result) ? $result : [$result];

        return !empty($result) ? $result : [];
    }

    /**
     * @return array
     */
    protected function formatFailure(): array
    {
        $body = $this->exception->getResponse()->getBody();
        $details = json_decode($body, true);

        return [
            'details' => $details,
            'code' => $this->exception->getCode(),
        ];
    }
}
