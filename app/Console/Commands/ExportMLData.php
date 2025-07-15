<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportMLData extends Command
{
    protected $signature = 'ml:export-data';
    protected $description = 'Export sales and customer data for ML pipeline';

    public function handle()
    {
        $this->call('export:sales-data');
        $this->call('export:customer-data');
        $this->info('ML data exported.');
    }
} 