<?php

namespace Scheduler;

use Closure;

class Scheduler
{
    protected $jobManager = null;
    protected $template = '';
    protected $crons = [];
    private static $instance = null;

    private function __construct()
    {
        $this->jobManager = new JobManager;
    }

    private static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function getCrons()
    {
        return $this->crons;
    }

    public function getJobManager()
    {
        return $this->jobManager;
    }

    public function pushJob($name, $job)
    {
        $this->getJobManager()->add($name, $job);
        return $this;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function pushCron($time, Closure $callback)
    {
        $this->crons[] = new Cron($time, $callback);
        return $this;
    }

    public function compileCrons($options)
    {
        $compiledJobs = [];
        foreach ($this->getCrons() as $cron) {
            $cron->compile($this->getJobManager());
            $compiledJobs = array_merge($cron->compileJobs($options), $compiledJobs);
        }
        return $compiledJobs;
    }

    public static function every($time, Closure $callback)
    {
        return static::getInstance()->pushCron($time, $callback);
    }

    public static function push($name, $job)
    {
        return static::getInstance()->pushJob($name, $job);
    }

    public static function cron($options)
    {
        return implode(static::getInstance()->compileCrons($options), "\n");
    }

    public static function run($output, $options)
    {
        (new CommandLine($output, $options))->run();
    }
}
