<?php

namespace Scheduler;

use Closure;

class Scheduler {
  private static $jobber = null;
  private static $template = "bash -l -c ':job'";
  private static $crons = [];
  private static $_instance = null;

   public static function getInstance ()
   {
     if (self::$_instance === null) {
       self::$_instance = new self;
     }

     return self::$_instance;
   }

  function job_type($name, $job) {
    if (self::$jobber == null) {
      self::$jobber = new Jobber();
    }

    self::$jobber->add($name, $job);
    return $this;
  }

  function job_template($template) {
    self::$template = $template;
  }

  function every($time, closure $callable) {
    $cron = (object) ["time" => $time, "callback" => $callable];
    self::$crons[] = $cron;
    return $this;
  }

  static function cron($options) {
    $compiledJobs = [];

    foreach (self::$crons as $cron) {
      call_user_func_array($cron->callback, [self::$jobber]);
      $cron->jobs = self::$jobber->flush();
      foreach ($cron->jobs as $job) {
        $job->template = str_replace(":task", $job->argument, $job->template);
        $job->template = str_replace(":path", $options["path"], $job->template);
        $compiledJobs[] = $cron->time . " " . str_replace(":job", $job->template, self::$template);
      }
    }
    return implode($compiledJobs, "\n");
  }

  static function run($output, $options) {
    (new CommandLine($output, $options))->run();
  }
}
