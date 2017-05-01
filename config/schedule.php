<?php
require __DIR__.'/../vendor/autoload.php';

use Scheduler\Scheduler;

// Define new job with job_type
Scheduler::push("pewpew", "cd :path && :task");
Scheduler::push("command", ":task");

Scheduler::every("* * * * *", function ($cron) {
    $cron->command("...");
    $cron->pewpew('echo "pewpew" >> log/test.log');
});
