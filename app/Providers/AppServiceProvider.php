<?php

namespace App\Providers;

use App\AI\AIProviderInterface;
use App\AI\Providers\MockAIProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AIProviderInterface::class, function () {

            // 🔥 للتطوير (سوريا / بدون API)
            return new MockAIProvider();

            // 🔥 عند تشغيل OpenAI لاحقًا
            // return new OpenAIProvider();
        });
    }
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
