<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Console\ServeCommand;
use App\Exceptions\ServeCommandHandler;

/**
 * Service Provider for ServeCommand error handling
 * 
 * This provider patches the ServeCommand to use our custom
 * error handling methods that prevent undefined array key errors.
 */
class ServeCommandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Bind custom handler for ServeCommand
        $this->app->bind(ServeCommand::class, function ($app) {
            return new class extends ServeCommand {
                /**
                 * Override getDateFromLine with error handling
                 */
                protected function getDateFromLine($line)
                {
                    return ServeCommandHandler::getDateFromLine($line);
                }

                /**
                 * Override getRequestPortFromLine with error handling
                 */
                public static function getRequestPortFromLine($line)
                {
                    return ServeCommandHandler::getRequestPortFromLine($line);
                }
            };
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Additional boot logic if needed
    }
}
