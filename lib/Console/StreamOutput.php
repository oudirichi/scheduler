<?php

namespace Scheduler\Console;

class StreamOutput extends Output
{
  public function __construct($stream) {
    $this->stream = $stream;
  }

  protected function doWrite($message, $newline)
  {
    if (false === @fwrite($this->stream, $message) || ($newline && (false === @fwrite($this->stream, PHP_EOL)))) {
      // should never happen
      throw new \Exception('Unable to write output.');
    }
    fflush($this->stream);
  }
}
