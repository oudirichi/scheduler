<?php

namespace Scheduler;

use closure;

function every($time, closure $callable)
{
  return Scheduler::getInstance()->every($time, $callable);
}

function job_type($name, $job)
{
  return Scheduler::getInstance()->job_type($name, $job);
}

function job_template($template)
{
  return Scheduler::getInstance()->job_template($template);
}
