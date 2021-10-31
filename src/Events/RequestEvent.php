<?php

namespace Requester\Events;

/**
 * Class RequestEvent
 * @package Requester
 */
abstract class RequestEvent implements RequestEventInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * RequestEvent constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
