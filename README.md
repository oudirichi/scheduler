# Scheduler - whenever gem alternative for PHP

## Installation

Install the latest version for global usage with
```bash
$ composer global require oudirichi/scheduler
```
or per project
```bash
$ composer require oudirichi/scheduler
```
## Getting started
```bash
$ cd /apps/my-great-project

# for global
$ shedulerize

# for specific project
$ vendor/bin/shedulerize
```

This will create an initial `config/schedule.php` file for you.

## The `scheduler` command

```bash
$ cd /apps/my-great-project

$ scheduler
```
This will simply show you your schedule.rb file converted to cron syntax. It does not read or write your crontab file; you'll need to do this in order for your jobs to execute:

```bash
$ scheduler --update-crontab
```

## Define your own job types
```php
<?php
Scheduler\job_type("pewpew", "cd :path && :task");

Scheduler\every("* * * * *", function($cron) {
  $cron->pewpew('echo "pewpew" >> log/test.log');
});

```
