<?php

namespace Scheduler;

class Job
{
    const DEFAULT_TEMPLATE = 'bash -l -c \':job\'';
    const JOB_PLACEHOLDER = ':job';
    const TASK_PLACEHOLDER = ':task';
    const PATH_PLACEHOLDER = ':path';
    const PATH_OPTION = 'path';

    protected $arguments;
    protected $template;
    protected $filledTemplate;
    protected $defaultPlaceholders = [];

    public function __construct($arguments, $template)
    {
        $this->arguments = $arguments;
        $this->template = $template;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    public function getFilltedTemplate()
    {
        return $this->filledTemplate;
    }

    public function getPlaceholders($options)
    {
        return [
            static::TASK_PLACEHOLDER => $this->arguments,
            static::PATH_PLACEHOLDER => $options[static::PATH_OPTION]
        ];
    }

    public function fillTemplate($options)
    {
        $filled = $this->getTemplate();
        foreach ($this->getPlaceholders($options) as $placeholder => $value) {
            $filled = str_replace($placeholder, $value, $filled);
        }
        $this->filledTemplate = str_replace(static::JOB_PLACEHOLDER, $filled, static::DEFAULT_TEMPLATE);
        return $this->getFilltedTemplate();
    }

    public function compile($cron, $options)
    {
        return $cron->getTime().' '.$this->fillTemplate($options);
    }
}
