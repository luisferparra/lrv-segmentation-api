<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;
use DB;
use Illuminate\Support\Facades\Schema;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        /**
         * Validador que chequea (por defecto) para la tabla de control, que el api_name no esté repetido en el sistema
         */
        Validator::extend('unique_slug', function ($attribute, $value, $parameters, $validator) {
            if(!is_array($parameters) || !isset($parameters[0])){
                throw new \RuntimeException("unique_slug needs table name");
            }
            $table = $parameters[0];
            $field = "slug";
            if(isset($parameters[1])){
                $field = $parameters[1];
            }
            $query = DB::table($table)->where($field, str_slug($value));
            if(isset($parameters[3])){
                $query->where($parameters[3], "!=", $parameters[2]);
            } elseif(isset($parameters[2])){
                $query->where('id', "!=", $parameters[2]);
            }
            return ($query->count() == 0);
        });


        /**
         * Validador que chequea (por defecto) para la tabla de control, que el name no esté repetido en el sistema
         */
        Validator::extend('unique_slug_without_middle_dash', function ($attribute, $value, $parameters, $validator) {
            if(!is_array($parameters) || !isset($parameters[0])){
                throw new \RuntimeException("unique_slug needs table name");
            }
            $table = $parameters[0];
            $field = "slug";
            if(isset($parameters[1])){
                $field = $parameters[1];
            }
            $query = DB::table($table)->where($field, str_replace('-','_',str_slug($value)));
            if(isset($parameters[3])){
                $query->where($parameters[3], "!=", $parameters[2]);
            } elseif(isset($parameters[2])){
                $query->where('id', "!=", $parameters[2]);
            }
            return ($query->count() == 0);
        });

        

        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Api\SegmentationSchema::class, function ($app) {
            return new \App\Api\SegmentationSchema(config('api-crm.table_val_postfix'));
        });
        $this->app->bind(\App\Admin\Stats\GetStat::class, function ($app) {
            return new \App\Admin\Stats\GetStat();
        });
        //$this->app->bind(\App\Api\SegmentationCounterInterface::class, \App\Api\SegmentationCounterV2::class);
        $this->app->bind(\App\Api\SegmentationCounterInterface::class, \App\Api\SegmentationCounter::class);
        
       /*  $this->app->bind(\App\Api\LoadDataClass::class, function ($app) {
            return new \App\Api\LoadDataClass(config('api-crm.table_val_postfix'));
        }); */
    }
}
