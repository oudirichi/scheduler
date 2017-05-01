<?php

namespace Scheduler;

use Closure;

class Cron
{
    protected $time;
    protected $callback;
    protected $jobs = [];

    public function __construct($time, Closure $callback)
    {
        $this->time = $time;
        $this->callback = $callback;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function getCallback()
    {
        return $this->callback;
    }

    public function setJobs($jobs)
    {
        $this->jobs = $jobs;
        return $this;
    }

    public function getJobs()
    {
        return $this->jobs;
    }

    public function runCallback($jobManager)
    {
        $callback = $this->getCallback();
        $output = $callback($jobManager);
        $this->setJobs($jobManager->flush());
        return $output;
    }

    public function compileJobs($options)
    {
        $compiledJobs = [];
        foreach ($this->getJobs() as $job) {
            $compiledJobs[] = $job->compile($this, $options);
        }
        return $compiledJobs;
    }

    public function compile($jobManager)
    {
        return $this->runCallback($jobManager);
    }
}
