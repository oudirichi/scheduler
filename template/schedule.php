<?php

  Scheduler\job_type("pewpew", "cd :path && /usr/local/bin/pewpew :task");
  Scheduler\job_type("command", ":task");
  // "bash -l -c ':job'"

  Scheduler\every("* * * * *", function($cron) {
    //  $cron->pewpew("…");
     $cron->command("…");
  });
  // ::every(Days::SUNDAY, ["at" => "2pm"], function() {
  //
  // })
  // every("1.hours", function() {
  //
  // });
