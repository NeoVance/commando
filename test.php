<?php

require_once(__DIR__.'/vendor/autoload.php');

use React\EventLoop\Factory;
use Rx\Scheduler;
use Rx\Subject\Subject;

Scheduler::setDefaultFactory(function () {
    return new Scheduler\ImmediateScheduler;
});

$state = [ 'name' => 'Devon' ];

$action = new Subject();

$store = $action
    ->startWith([])
    ->scan(function ($os, $a) {
        if ($a['type'] == 'NAME_CHANGE') {
            $state = [ 'name' => $a['payload'] ];
            return $state;
        }
        return $os;
    });
    
$actionDispatcher = function($func) use ($action) {
    return function(...$args) use ($func, $action) {
        $action->onNext(call_user_func($func, ...$args));
    };
};

$changeName = $actionDispatcher(function($payload) {
    return [
        'type' => 'NAME_CHANGE',
        'payload' => $payload
    ];
});

$store->subscribe(new Rx\Observer\CallbackObserver(
    function ($value) { echo "Next value: " . print_r($value, true) . "\n"; },
    function ($error) { echo "Exception: " . $error->getMessage() . "\n"; },
    function ()       { echo "Complete!\n"; }
));

$changeName("steven");
