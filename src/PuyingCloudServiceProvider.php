<?php

namespace Yan\PuyingCloudSdk;

class PuyingCloudServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    /**
     * 在注册后进行服务的启动。
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/puyingcloud.php' => config_path('puyingcloud.php'),
        ]);
    }

    public function register()
    {
        $this->app->singleton(PuyingCloudSdk::class, function () {
            $config = config('puyingcloud');

            return new PuyingCloudSdk($config);
        });

        $this->app->alias(PuyingCloudSdk::class, 'puyingcloud');
    }

    public function provides()
    {
        return [PuyingCloudSdk::class, 'puyingcloud'];
    }
}