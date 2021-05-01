<?php


namespace AllCoinTrack\ServiceProvider;


use Http\Adapter\Guzzle7\Client;
use Http\Client\HttpClient;
use Illuminate\Support\ServiceProvider;

class HttpClientServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(HttpClient::class, function () {
            return new Client();
        });
    }
}
