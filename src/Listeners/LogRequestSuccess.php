<?php

namespace Requester\Listeners;

use Illuminate\Support\Facades\Log;
use Requester\Events\RequestSuccess;

/**
 * Class LogRequestSuccess
 * @package Requester\Listeners
 */
class LogRequestSuccess extends RequestListener
{
    /**
     * @param RequestSuccess $event
     * @return bool
     */
    public function handle(RequestSuccess $event)
    {
        $data = $event->getData();

        if (empty($data['channel'])) {
            return false;
        }

        $title = sprintf('%s %s', $data['method'], $data['end_point']);

        $response = $data['response']->data();

        $requestTitle = sprintf('%s %s', $title, 'REQUEST');
        Log::channel($data['channel'])->info($requestTitle, [
            'basic' => $data['basic'],
            'params' => $data['params'],
            'headers' => $data['headers']
        ]);

        $responseTitle = sprintf('%s %s', $title, 'RESPONSE');
        Log::channel($data['channel'])->info($responseTitle, [
            'response' => $response,
        ]);
    }
}
