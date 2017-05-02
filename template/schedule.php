<?php
require __DIR__.'/../vendor/autoload.php';

use Scheduler\Scheduler;

// Define new job with jobType
Scheduler::jobType("pewpew", "cd :path && :task");
Scheduler::jobType("command", ":task");

Scheduler::every("* * * * *", function ($cron) {
    // and use them
    // $cron->command("...");
    // $cron->pewpew('echo "pewpew" >> log/test.log');
});
