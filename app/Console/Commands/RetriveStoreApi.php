<?php

namespace App\Console\Commands;

use App\Jobs\FetchAndRetriveApi;
use Illuminate\Console\Command;
use App\Models\Coin;
use Illuminate\Support\Facades\Http;

class RetriveStoreApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'retrive:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to retrive data and store data in the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dispatch(new FetchAndRetriveApi())->onQueue('default');
        $this->info('worked');
    }
}
