<?php

namespace Requester;

use Illuminate\Support\Facades\Event;
use Requester\Events\RequestFail;
use Requester\Events\RequestSuccess;
use Illuminate\Support\ServiceProvider;
use Requester\Listeners\LogRequestFailed;
use Requester\Listeners\LogRequestSuccess;

/**
 * Class EventServiceProvider
 * @package Requester
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $eventListeners = [
        RequestFail::class => [
            LogRequestFailed::class
        ],
        RequestSuccess::class => [
            LogRequestSuccess::class
        ],
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->eventListeners as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                Event::listen($event, $listener);
            }
        }
    }
}
