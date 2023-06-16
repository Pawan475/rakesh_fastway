
# Coingecho API - Fetch and Store in the database.

A brief description of what this task does and who it's for




## Documentation

[Documentation](https://www.coingecko.com/en/api/documentation)


## Setup database Locally

`DB_DATABASE` fastway

`DB_USERNAME` username

`DB_PASSWORD` *********

## Run Locally

Go to the project directory

```bash
  cd fastway
```

Install require packages

```bash
  composer require flipbox/lumen-generator
  composer require guzzlehttp/guzzle
```

Create model

```bash
  php artisan make:model Coin
```

Create schemas in migration

```bash
  php artisan make:migration --create-coins create_coins_table
  php artisan queue:table    
```
- Creating Jobs. By default, all queued jobs are stored in the app/Jobs directory.

Migrate tables in database using artisan command

```bash
  php artisan migrate
```


## Environment Variables

To run this project, you will need to add the following environment variables to your .env file

`API_KEY` php artisan key:generate
`QUEUE_CONNECTION` database

## Configurations

Add line in app.php 

```bash
$app->configure('queue');
```

Uncomment two lines in app.php

```bash
$app->withFacades();
$app->withEloquent();
```


## Running Tests

To run tests, run the following command

```bash
php -s localhost:8000 -t public
```

## Running Queue Jobs

A create job, run the following command

```bash
php artisan make:job FetchAndRetriveApi

----------------------------------------

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
            $api_url = "paste api_url here";

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




```

## The commands store the data in a database.

A create command, run the following command

```bash
php artisan make:command RetriveStoreApi
----------------------------------------

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

```

After create job and command, running test the following command

```bash
    php artisan queue:listen
    php artisan retrive:store
```



## Database schemas

Here are some schemas

### 1) coin table

```bash
  Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->string('coin_id');
            $table->string('symbol');
            $table->string('name');
            $table->longText('platforms')->nullable();            
            $table->timestamps();
        });
```

### 2) job table
```bash
 Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
```



## Features

- Queue makes your web app run better by sending time-consuming tasks to the background. This frees up resources and lets your app respond to user requests faster. This can make the whole user experience better and make it easier for users to stick around