<?php


namespace CrackerSw\ChinaUmsPay;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(ChinaUmsPay::class, function () {

            return new ChinaUmsPay(config("chinaumspay.default"));
        });
        $this->app->alias(ChinaUmsPay::class, 'china_ums_pay');
    }

    public function provides()
    {
        return [ChinaUmsPay::class, 'china_ums_pay'];
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . "/config/chinaumspay.php" => config_path("chinaumspay.php"),
        ]);
    }

}