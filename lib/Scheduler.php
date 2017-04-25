<?php

namespace Scheduler;

use Closure;

class Scheduler {
  private static $jobber = null;
  private static $template = "bash -l -c ':job'";
  private static $crons = [];
  private static $configuration;
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
    $cron = new Cron();
    $cron->time = $time;
    $cron->callback = $callable;
    self::$crons[] = $cron;
    return $this;
  }

  static function cron($options) {
    $compiledJobs = [];

    foreach (self::$crons as $cron) {
      call_user_func_array($cron->callback, [self::$jobber]);
      $cron->jobs = self::$jobber->flush();
      foreach ($cron->jobs as $job) {
        $job["template"] = str_replace(":task", $job["argument"], $job["template"]);
        $job["template"] = str_replace(":path", $options["path"], $job["template"]);
        $compiledJobs[] = $cron->time . " " . str_replace(":job", $job["template"], self::$template);
      }
    }
    return implode($compiledJobs, "\n");
  }

  static function run($path) {
    $input = new Console\ArgvInput();
    $output = new Console\ConsoleOutput();

    $arguments = $input->getArguments();
    if (count($arguments) < 1) {
      $output->writeln(Self::help());
      exit;
    }
    $arguments["path"] = $path;

    if (!file_exists($path . "/config/schedule.php")) {
      $output->writeln($path . "/config/schedule.php doesn't exist. Be sure to be in main folder");
      exit(1);
    }

    require $path . "/config/schedule.php";

    if ($input->hasArgument("--help")) {
      $output->writeln(Self::help());
      exit(0);
    }

    if ($input->hasArgument("-i")) {
      $arguments["update"] = true;
    }

    if ($input->hasArgument("-c")) {
      $arguments["clear"] = true;
    }

    if ($input->hasArgument("-w")) {
      $arguments["write"] = true;
    }

    if ($input->hasArgument("--dry-run") || $input->hasArgument("-l")) {
      $arguments["dry-run"] = true;
    }

    (new CommandLine($arguments))->run($output);

  }

  static function install($currentDirectory) {
    $output = new Console\ConsoleOutput();

    var_dump($currentDirectory);
    $configPath = $currentDirectory . "/config";
    if (!is_dir($configPath)) {
      $output->writeln("Creating config folder...");
      mkdir($configPath, 0755);
    }

    $schedulePath = $configPath . "/schedule.php";

    if (!file_exists($schedulePath)) {
      $output->writeln("Creating 'schedule.php' file...");
      copy(dirname(__DIR__) . "/template/schedule.php", $schedulePath);
      chmod($schedulePath, 0755);
    }

    $binFile = $currentDirectory . "/scheduler";
    if (!file_exists($binFile)) {
      $output->writeln("Creating 'scheduler' file...");
      copy(dirname(__DIR__) . "/template/scheduler", $binFile);
      chmod($binFile, 0755);
    }

    $output->writeln("Everything done! Enjoy!");
  }

  static function help() {
    $message = "
    /================\
    | Scheduler Help |
    \================/\n
    When running command, be sure to be in root folder of your project\n
    -l, --dry-run : list schedule file as cron syntax
    -i : update crontab with current crontab prepended with schedule file
    -w : clear crontab and adding schedule file
    -c : clear crontab\n";

    return $message;
  }
}
