<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Eureka\EurekaClient;

class EurekaRegisterJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new EurekaClient([
            'eurekaDefaultUrl' => env('EUREKA_SERVER_URL', 'http://localhost:8761/eureka'),
            'hostName' => env('APP_URL', 'http://localhost'), // Using APP_URL as hostname
            'appName' => env('APP_NAME', 'Laravel'), // Fetching APP_NAME
            'ip' => env('APP_URL', 'http://localhost'), // Assuming the app runs on localhost
            'port' => env('APP_PORT', '8080'), // Extract port from APP_URL
            'homePageUrl' => env('APP_URL', 'http://localhost') . '/info',
            'statusPageUrl' => env('APP_URL', 'http://localhost') . '/info',
            'healthCheckUrl' => env('APP_URL', 'http://localhost') . '/health'
        ]);

        $client->heartbeat();
    }
}
