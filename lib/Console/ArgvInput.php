<?php

namespace Scheduler\Console;

class ArgvInput extends Input
{
  public function __construct(array $argv = null)
  {
    if (null === $argv) {
      $argv = $_SERVER["argv"];
    }

    // strip the application name
    array_shift($argv);
    $this->parse($argv);
  }
}
