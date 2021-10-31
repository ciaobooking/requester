<?php

namespace Requester\Events;

/**
 * Interface RequestEventInterface
 * @package Requester
 */
interface RequestEventInterface
{
    /**
     * @return array
     */
    public function getData(): array;
}
