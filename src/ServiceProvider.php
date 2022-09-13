<?php


namespace CrackerSw\ChinaUmsPay;


use CrackerSw\ChinaUmsPay\Request\ChinaUmsOrder;
use CrackerSw\ChinaUmsPay\Request\ChinaUmsQrcodeOrder;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;
    public function register(){
        $provides  = [
            'china_ums_qrc_order' => ChinaUmsQrcodeOrder::class,
            'china_ums_order' => ChinaUmsOrder::class,
            'china_ums_funds' => ChinaUmsFunds::class,
        ];
        foreach ($provides as $name => $provide) {
            $this->app->singleton($provide,function () use($provide) {
                return new $provide(config("chinaumspay.default"));
            });
            $this->app->alias($provide,$name);
        }
    }

    public function provides(){
        return [
            ChinaUmsOrder::class,
            ChinaUmsQrcodeOrder::class,
            'china_ums_order',
            'china_ums_qrc_order'
        ];
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . "/config/chinaumspay.php" => config_path("chinaumspay.php"),
        ]);
    }
}