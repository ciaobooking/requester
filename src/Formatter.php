<?php

namespace Requester;

use Illuminate\Support\Arr;

/**
 * Class Formatter
 * @package Requester
 */
class Formatter
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
     * @var array
     */
    protected $result = [];

    /**
     * @param array $basic
     * @return Formatter
     */
    public function setBasic(array $basic): self
    {
        $this->basic = $basic;

        return $this;
    }

    /**
     * @param array $params
     * @return Formatter
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @param array $headers
     * @return Formatter
     */
    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param string $method
     * @return array
     */
    public function format(string $method): array
    {
        $this->formatBasic();

        if ('GET' === $method) {
            $this->formatGet();
        }
        if ('DELETE' === $method) {
            $this->formatDelete();
        }
        if ('POST' === $method) {
            $this->formatPost();
        }
        if ('PUT' === $method) {
            $this->formatPut();
        }
        if ('PATCH' === $method) {
            $this->formatPatch();
        }

        return $this->result;
    }

    /**
     * @return Formatter
     */
    protected function formatGet(): self
    {
        $this->formatBasic();

        Arr::forget($this->result, 'headers.Content-Type');

        if (!empty($this->params)) {
            $this->result['query'] = $this->params;
        }

        return $this;
    }

    /**
     * @return Formatter
     */
    protected function formatDelete(): self
    {
        return $this->formatBasic();
    }

    /**
     * @return Formatter
     */
    protected function formatPost(): self
    {
        $this->formatBasic();

        if (!empty($this->params)) {
            if (Arr::get($this->result, 'headers.Content-Type') === 'application/json') {
                $this->result['json'] = $this->params;
            }
            if (Arr::get($this->result, 'headers.Content-Type') === 'application/x-www-form-urlencoded') {
                $this->result['form_params'] = $this->params;
            }
            if (Arr::get($this->result, 'headers.Content-Type') === 'text/xml; charset=UTF8') {
                $this->result['body'] = $this->params['body'];
            }
        }

        return $this;
    }

    /**
     * @return Formatter
     */
    protected function formatPut(): self
    {
        $this->formatBasic();

        if (!empty($this->params)) {
            if (Arr::get($this->result, 'headers.Content-Type') === 'application/json') {
                $this->result['json'] = $this->params;
            }
            if (Arr::get($this->result, 'headers.Content-Type') === 'application/x-www-form-urlencoded') {
                $this->result['form_params'] = $this->params;
            }
        }

        return $this;
    }

    /**
     * @return Formatter
     */
    protected function formatPatch(): self
    {
        $this->formatBasic();

        if (!empty($this->params)) {
            if (Arr::get($this->result, 'headers.Content-Type') === 'application/json') {
                $this->result['json'] = $this->params;
            }
            if (Arr::get($this->result, 'headers.Content-Type') === 'application/x-www-form-urlencoded') {
                $this->result['form_params'] = $this->params;
            }
        }

        return $this;
    }

    /**
     * @return Formatter
     */
    protected function formatBasic(): self
    {
        $this->result = [
            'headers' => [
                'Content-Type' => 'application/json'
            ]
        ];

        $this->result = array_merge($this->result, $this->basic);

        if (!empty($this->headers)) {
            $this->result['headers'] = array_merge($this->result['headers'], $this->headers);
        }

        return $this;
    }
}
