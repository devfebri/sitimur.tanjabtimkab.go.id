<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PengajuanOpenController;

class AutoChangeExpiredPengajuanStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pengajuan:auto-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically change pengajuan status from 14/34 to 88 after 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-change expired pengajuan status...');
        
        $controller = new PengajuanOpenController();
        $result = $controller->autoChangeExpiredStatus();
        
        if ($result['success']) {
            $this->info($result['message']);
        } else {
            $this->error($result['message']);
        }
        
        return Command::SUCCESS;
    }
}
