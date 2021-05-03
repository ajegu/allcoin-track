<?php


namespace AllCoinTrack\ServiceProvider;


use AllCoinTrack\Repository\AssetRepository;
use AllCoinTrack\Repository\AssetRepositoryInterface;
use AllCoinTrack\Repository\PriceRepository;
use AllCoinTrack\Repository\PriceRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AssetRepositoryInterface::class, AssetRepository::class);
        $this->app->bind(PriceRepositoryInterface::class, PriceRepository::class);
    }
}