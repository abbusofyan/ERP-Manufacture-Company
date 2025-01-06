<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use libphonenumber\PhoneNumberUtil;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\Order;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        date_default_timezone_set(config('app.timezone'));

        Validator::extend('at_least_one_selected', function ($attribute, $value, $parameters, $validator) {
            foreach ($value as $item) {
                if ($item['selected']) {
                    return true;
                }
            }
            return false;
        });

        Validator::extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            $phoneUtil = PhoneNumberUtil::getInstance();

            try {
                $parsedNumber = $phoneUtil->parse($value, null);
                return $phoneUtil->isValidNumber($parsedNumber);
            } catch (\libphonenumber\NumberParseException $e) {
                return false;
            }
        });

        Relation::morphMap([
            'order' => Order::class,
        ]);
    }
}
