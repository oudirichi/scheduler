<?php

namespace Scheduler\Console;

class Input
{
  public $arguments = [];

  public function parse($arguments)
  {
    $this->arguments = (array) $arguments;
  }

  public function getArguments()
  {
    return $this->arguments;
  }

  public function hasArgument($name)
  {
    return in_array($name, $this->arguments);
  }
}
