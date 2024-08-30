<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Currency as CurrencyModel;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class Currency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all currencies';

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

        $rates = file_get_contents('http://www.floatrates.com/daily/eur.json');
        Storage::disk('local')->put('exchange-rates.json', $rates);


        Log::info('start cron currency');

        $currencies = [
            'eur',
            'usd',
            'ron',
            'gbp'
        ];

        $ratesFs = Storage::disk('local')->get('exchange-rates.json');
        $ratesFs = json_decode($ratesFs, true);

        $newCurrencies = [];

        foreach ($currencies as $currency) {
            foreach ($currencies as $_currency) {

                $rate = 1;

                if ($currency != $_currency) {
                    if ($_currency == 'eur') {
                        $rate = number_format(1 / $ratesFs[$currency]['rate'], 2);
                    } else {
                        $rate = number_format($ratesFs[$_currency]['rate'], 2);
                    }
                }

                $newCurrencies[] = [
                    'cur_from' => $currency,
                    'cur_to' => $_currency,
                    'rate' => $rate
                ];
            }
        }

        foreach ($newCurrencies as $newCurrency) {

            $currencyDb = CurrencyModel::where([
                ['cur_from', $newCurrency['cur_from']],
                ['cur_to', $newCurrency['cur_to']]
            ])->get()->first();

            //check exists

            if ($currencyDb) {
                try {
                    $currencyDb->rate = $newCurrency['rate'];
                    $currencyDb->save();
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }

            } else {
                try {
                    $new_row = new CurrencyModel();
                    $new_row->cur_from = $newCurrency['cur_from'];
                    $new_row->cur_to = $newCurrency['cur_to'];
                    $new_row->rate = $newCurrency['rate'];
                    $new_row->save();
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }

            }
        }
    }

}
