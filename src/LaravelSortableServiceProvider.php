<?php

namespace Hichxm\LaravelSortable;

use Illuminate\Database\Schema\Blueprint;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSortableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sortable');
    }

    public function bootingPackage(): void
    {
        Blueprint::macro('orderColumn', function ($column = 'order') {
            /** @var Blueprint $this */
            return $this->unsignedInteger($column)->nullable();
        });

        Blueprint::macro('dropOrderColumn', function ($column = 'order') {
            /** @var Blueprint $this */
            return $this->dropColumn($column);
        });
    }
}