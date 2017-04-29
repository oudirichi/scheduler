<?php

namespace Scheduler;

use closure;

class Jobber
{
  private $jobs = [];
  private $memory = [];

  public function add($name, $job) {
    if(!in_array($name, $this->jobs)) {
      $this->jobs[$name] = $job;
    }
  }

  public function flush() {
    $temp = $this->memory;
    $this->memory = [];

    return $temp;
  }

  public function __call($name, $arguments)
  {
    if (array_key_exists($name, $this->jobs)) {
      $template = $this->jobs[$name];
      $this->memory[] = (object) ["argument" => $arguments[0], "template" => $template];
    }
  }
}
