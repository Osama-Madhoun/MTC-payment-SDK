<?php

namespace MTC\Payments;

use Illuminate\Support\ServiceProvider;
use MTC\Payments\Classes\FawryPayment;
use MTC\Payments\Classes\HyperPayPayment;
use MTC\Payments\Classes\KashierPayment;
use MTC\Payments\Classes\PaymobPayment;
use MTC\Payments\Classes\PayPalPayment;
use MTC\Payments\Classes\PaytabsPayment;
use MTC\Payments\Classes\ThawaniPayment;
use MTC\Payments\Classes\TapPayment;
use MTC\Payments\Classes\OpayPayment;
use MTC\Payments\Classes\PaymobWalletPayment;

class MTCPaymentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configure();

        $langPath = 'vendor/payments';
        $langPath = (function_exists('lang_path'))
            ? lang_path($langPath)
            : resource_path('lang/' . $langPath);

        $this->registerPublishing($langPath);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'MTC');




        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/MTC'),
            __DIR__ . '/../config/MTC-payments.php' => config_path('MTC-payments.php'),
            __DIR__ . '/../resources/lang' => $langPath,
        ], 'MTC-payments-all');

        $this->registerTranslations($langPath);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(PaymobPayment::class, function () {
            return new PaymobPayment();
        });
        $this->app->bind(FawryPayment::class, function () {
            return new FawryPayment();
        });
        $this->app->bind(ThawaniPayment::class, function () {
            return new ThawaniPayment();
        });
        $this->app->bind(PaypalPayment::class, function () {
            return new PaypalPayment();
        });
        $this->app->bind(HyperPayPayment::class, function () {
            return new HyperPayPayment();
        });
        $this->app->bind(KashierPayment::class, function () {
            return new KashierPayment();
        });
        $this->app->bind(TapPayment::class, function () {
            return new TapPayment();
        });
        $this->app->bind(OpayPayment::class, function () {
            return new OpayPayment();
        });
        $this->app->bind(PaymobWalletPayment::class, function () {
            return new PaymobWalletPayment();
        });
        $this->app->bind(PaytabsPayment::class, function () {
            return new PaytabsPayment();
        });
    }

    /**
     * Setup the configuration for MTC Payments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/MTC-payments.php',
            'MTC-payments'
        );
    }
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations($langPath)
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'MTC');
        $this->loadTranslationsFrom($langPath, 'MTC');
    }
    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing($langPath)
    {
        $this->publishes([
            __DIR__ . '/../config/MTC-payments.php' => config_path('MTC-payments.php'),
        ], 'MTC-payments-config');

        $this->publishes([
            __DIR__ . '/../resources/lang' => $langPath,
        ], 'MTC-payments-lang');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/payments'),
        ], 'MTC-payments-views');
    }
}
