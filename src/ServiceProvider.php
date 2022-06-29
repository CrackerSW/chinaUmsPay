<?php


namespace CrackerSw\ChinaUmsPay;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;
    public function register(){
        $this->app->singleton(ChinaUmsPay::class,function (){
            $option = [

            ];

            return new ChinaUmsPay(config('china_ums_pay.key'));
        });
        $this->app->alias(ChinaUmsPay::class,'china_ums_pay');
    }

    public function provides(){
        return [ChinaUmsPay::class,'china_ums_pay'];
    }

}