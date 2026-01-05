<?php

namespace App\Providers;

use App\Domain\Contracts\AdminRepositoryInterface;
use App\Infrastructure\Repositories\AdminRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AdminRepositoryInterface::class,
            AdminRepository::class
        );

        $this->app->singleton(FileUploadService::class);

        $this->app->bind(CreateAdmin::class);
        $this->app->bind(UpdateAdmin::class);
        $this->app->bind(DeleteAdmin::class);
        $this->app->bind(SearchAdmin::class);
        $this->app->bind(ShowAdmin::class);
    }
}