<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\CurrencyHelper;

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
        // Register Blade directives for currency formatting
        Blade::directive('rwf', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::rwf($expression); ?>";
        });

        Blade::directive('rwfDecimals', function ($expression) {
            return "<?php echo App\Helpers\CurrencyHelper::rwfWithDecimals($expression); ?>";
        });
    }
}
