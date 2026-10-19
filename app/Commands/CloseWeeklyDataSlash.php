<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;

class CloseWeeklyDataSlash extends CloseWeeklyData
{
    protected $name = 'cronjob/closeweeklydata';
}