<?php

namespace Scheduler\Console;

abstract class output
{
  public function writeln($messages)
  {
    $this->write($messages, true);
  }

  function write($messages, $newline = false)
  {
    $messages = (array) $messages;

    foreach ($messages as $message) {
      $this->doWrite($message, $newline);
    }
  }

  abstract protected function doWrite($message, $newline);
}
