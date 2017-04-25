<?php

namespace Scheduler\Console;

class ConsoleOutput extends StreamOutput
{
  private $stderr;


    public function __construct()
    {
        parent::__construct($this->openOutputStream());
    }

    /**
     * Returns true if current environment supports writing console output to
     * STDOUT.
     *
     * @return bool
     */
    protected function hasStdoutSupport()
    {
        return false === $this->isRunningOS400();
    }

    /**
     * Checks if current executing environment is IBM iSeries (OS400), which
     * doesn't properly convert character-encodings between ASCII to EBCDIC.
     *
     * @return bool
     */
    private function isRunningOS400()
    {
        $checks = array(
            function_exists('php_uname') ? php_uname('s') : '',
            getenv('OSTYPE'),
            PHP_OS,
        );

        return false !== stripos(implode(';', $checks), 'OS400');
    }

    private function openOutputStream()
    {
        if (!$this->hasStdoutSupport()) {
            return fopen('php://output', 'w');
        }

        return @fopen('php://stdout', 'w') ?: fopen('php://output', 'w');
    }

}
