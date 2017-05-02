<?php

namespace Scheduler;

use closure;

class JobManager
{
    protected $commands = [];
    protected $memory = [];

    public function add($name, $command)
    {
        if (!$this->containsCommand($name)) {
            $this->commands[$name] = $command;
        }
    }

    public function flush()
    {
        $temp = $this->memory;
        $this->memory = [];

        return $temp;
    }

    public function containsCommand($name)
    {
        return array_key_exists($name, $this->getCommands());
    }

    public function getCommands()
    {
        return $this->commands;
    }

    public function getCommand($name)
    {
        return $this->getCommands()[$name];
    }

    public function memorizeJob($name, $arguments)
    {
        $this->memory[] = new Job($arguments[0], $this->getCommand($name));
    }

    public function __call($name, $arguments)
    {
        if ($this->containsCommand($name)) {
            $this->memorizeJob($name, $arguments);
        }
    }
}
