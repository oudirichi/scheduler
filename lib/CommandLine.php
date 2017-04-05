<?php

namespace Whenever;

class CommandLine {
  private $options = [];

  function __construct(array $options) {
    $this->options = array($options);
    $this->options["crontab_command"] = isset($this->options["crontab_command"]) ? $this->options["crontab_command"] : "crontab";
    $this->options["file"] = isset($this->options["file"]) ? $this->options["file"] : "config/schedule.php";
    $this->options["identifier"] = isset($this->options["identifier"]) ? $this->options["identifier"] : $this->default_identifier();
    $this->create_timestamp();
    $this->run();
  }

  function default_identifier() {
    return __FILE__;
  }

  function create_timestamp() {
    $timezone = new \DateTimeZone('UTC');
    $this->timestamp = \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), $timezone)
                ->setTimezone($timezone)
                ->format('Y-m-d H:i:s');
  }

  function run() {
    $this->write_crontab($this->updated_crontab());


    //   if @options[:update] || @options[:clear]
    //     write_crontab(updated_crontab)
    //   elsif @options[:write]
    //     write_crontab(whenever_cron)
    //   else
    //     puts Whenever.cron(@options)
    //     puts "## [message] Above is your schedule file converted to cron syntax; your crontab file was not updated."
    //     puts "## [message] Run `whenever --help' for more options."
    //     exit(0)
    //   end
  }

  function read_crontab() {
    if (!isset($this->current_crontab)) {
      $command = [];
      $command[] = $this->options["crontab_command"];
      $command[] = "-l";
      if (isset($this->options["user"])) $command[] = "-u " . $this->options["user"];

      $this->current_crontab = shell_exec(implode(" ", $command) . " 2> /dev/null");
    }
    return $this->current_crontab;
  }

  function write_crontab($content) {
    echo $content;

    // $handle = popen('crontab -', 'r+');
    // fwrite($handle, $content);
    // fclose($handle);
  }

  function updated_crontab() {
    $read_crontab = $this->read_crontab();
    # Check for unopened or unclosed identifier blocks
    if (preg_match("/^{$this->regex_comment_open()}\s/m", $read_crontab) && !preg_match("/^{$this->regex_comment_close()}\s/m", $read_crontab)) {
      throw new \Exception("[fail] Unclosed indentifier; Your crontab file contains open but not close tag");
    }

    if (!preg_match("/^{$this->regex_comment_open()}\s/m", $read_crontab) && preg_match("/^{$this->regex_comment_close()}\s/m", $read_crontab)) {
      throw new \Exception("[fail] Unclosed indentifier; Your crontab file contains closing but no open tag");
    }

    # if their is a blocks, remove it
    if (preg_match("/^{$this->regex_comment_open()}\s/m", $read_crontab) && preg_match("/^{$this->regex_comment_close()}\s/m", $read_crontab)) {
      $read_crontab = preg_replace("/^{$this->regex_comment_open()}\s*$[\s\S]*?^{$this->regex_comment_close()}\s*$/m", '', $read_crontab);
    }

    $read_crontab = rtrim($read_crontab);

    return implode("\n\n", [$read_crontab, $this->generate_cron()]);
  }

  function generate_cron() {
    if (!isset($this->generated_cron)) {
      $this->generated_cron = implode("\n", [$this->comment_open(), CronManager::cron($this->options), $this->comment_close()]) . "\n";
    }

    return $this->generated_cron;
  }


  function comment_base($include_timestamp = true) {
    $ret = "CronManager generated tasks for: " . $this->options["identifier"];
    if($include_timestamp)
      $ret. " at: " . $this->timestamp;

    return $ret;
  }

  function comment_open($include_timestamp = true) {
    return "# Begin " . $this->comment_base($include_timestamp);
  }

  function comment_close($include_timestamp = true) {
    return "# End " . $this->comment_base($include_timestamp);
  }

  function regex_comment_open() {
    return $this->safe_regex($this->comment_open(false));
  }

  function regex_comment_close() {
    return $this->safe_regex($this->comment_close(false));
  }

  function safe_regex($content) {
    return str_replace('.', '\.', str_replace('/', '\/', $content));
  }

  function timestamp_regex() {
    return " at: \\d{4}-\\d{2}-\\d{2} \\d{2}:\\d{2}:\\d{2}";
  }
}
