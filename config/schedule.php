<?php

  // Define new job with job_type
  Scheduler\job_type("pewpew", "cd :path && :task");
  Scheduler\job_type("command", ":task");

  Scheduler\every("* * * * *", function($cron) {
    // and use them
    // $cron->command("...");
    // $cron->pewpew('echo "pewpew" >> log/test.log');
  });
