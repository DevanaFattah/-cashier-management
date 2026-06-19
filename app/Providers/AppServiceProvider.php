<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\Models\Shop;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        View::composer('*', function ($view) {
            try {
                $shop = Shop::first();
                $view->with('shopName', $shop ? $shop->name : "MajuJaya");
                $view->with('shopAddress', $shop ? $shop->address : "Jl. ");
            } catch (\Exception $e) {
                $view->with('shopName', "MajuJaya");
                $view->with('shopAddress', "Jl. ");
            }
        });

        View::composer(['components.layouts.main', 'components.layouts.sidebar'], function ($view) {
            try {
                $shop = Shop::with('setting')->first();
                $view->with('logo', $shop ? $shop->path_logo : false);
                $view->with('theme', $shop && $shop->setting ? $shop->setting->theme : 'ember');
                $view->with('shop', $shop ? $shop->id : '1');
            } catch (\Exception $e) {
                $view->with('logo', false);
                $view->with('theme', 'ember');
                $view->with('shop', '1');
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
