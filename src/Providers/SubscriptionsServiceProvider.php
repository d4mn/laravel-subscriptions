<?php

declare(strict_types=1);

namespace MaxAl\Subscriptions\Providers;

use MaxAl\Subscriptions\Models\Plan;
use Illuminate\Support\ServiceProvider;
use MaxAl\Subscriptions\Models\PlanFeature;
use MaxAl\Subscriptions\Models\PlanSubscription;
use MaxAl\Subscriptions\Models\PlanSubscriptionUsage;
use MaxAl\Subscriptions\Console\Commands\MigrateCommand;
use MaxAl\Subscriptions\Console\Commands\PublishCommand;
use MaxAl\Subscriptions\Console\Commands\RollbackCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.maxal.subscriptions.migrate',
        PublishCommand::class => 'command.maxal.subscriptions.publish',
        RollbackCommand::class => 'command.maxal.subscriptions.rollback',
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            realpath(__DIR__ . '/../../config/config.php'),
            'maxal.subscriptions'
        );

        // Register bindings in the service container
        $this->app->bind('maxal.subscriptions.plan', Plan::class);
        $this->app->bind('maxal.subscriptions.plan_feature', PlanFeature::class);
        $this->app->bind('maxal.subscriptions.plan_subscription', PlanSubscription::class);
        $this->app->bind('maxal.subscriptions.plan_subscription_usage', PlanSubscriptionUsage::class);

        // Register console commands
        $this->commands(array_values($this->commands));
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/config.php' => config_path('maxal/subscriptions.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }
}
