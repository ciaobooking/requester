<?php

namespace Requester\Listeners;

use Illuminate\Support\Facades\Log;
use Requester\Events\RequestFail;

/**
 * Class LogRequestFailed
 * @package Requester\Listeners
 */
class LogRequestFailed extends RequestListener
{
    /**
     * @param RequestFail $event
     * @return bool
     */
    public function handle(RequestFail $event)
    {
        $data = $event->getData();

        if (empty($data['channel'])) {
            return false;
        }

        $title = sprintf('%s %s', $data['method'], $data['end_point']);

        $response = $data['response']->errors();

        $requestTitle = sprintf('%s %s', $title, 'REQUEST');
        Log::channel($data['channel'])->error($requestTitle, [
            'basic' => $data['basic'],
            'params' => $data['params'],
            'headers' => $data['headers']
        ]);

        $responseTitle = sprintf('%s %s', $title, 'RESPONSE');
        Log::channel($data['channel'])->error($responseTitle, [
            'response' => $response,
        ]);
    }
}
