<?php

  // Define new job with job_type
  // Scheduler\job_type("pewpew", "cd :path && /usr/local/bin/pewpew :task");
  Scheduler\job_type("command", ":task");

  Scheduler\every("* * * * *", function($cron) {
     $cron->command("…");

     // and use them in your task
     //  $cron->pewpew("…");
  });
