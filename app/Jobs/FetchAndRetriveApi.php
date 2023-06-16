<?php

namespace App\Jobs;

use App\Models\Coin;
use Illuminate\Support\Facades\Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
class FetchAndRetriveApi extends Job
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $api_url = "https://api.coingecko.com/api/v3/coins/list?include_platform=true";

            $res = Http::get($api_url);
            $data = json_decode($res->body());
            foreach($data as $coin){
                $coin = (array)$coin;
                $platform= (array)$coin['platforms'];
                $coinsData =  Coin::updateOrCreate(
                    ['coin_id'=>$coin['id']],
                    [
                        'coin_id'=>$coin['id'],
                        'symbol'=>$coin['symbol'],
                        'name'=>$coin['name'],
                        'platforms'=>json_encode($platform),
                    ],
                );
            }
            return 1;
        } catch (Exception $e) {
           return 0;
        }
    }
}
