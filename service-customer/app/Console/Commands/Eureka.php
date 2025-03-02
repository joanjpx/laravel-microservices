<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Eureka\EurekaClient;

class Eureka extends Command
{
    protected $signature = 'app:eureka-register';
    protected $description = 'Register service with Eureka';

    public function handle()
    {
        $eurekaUrl = env('EUREKA_SERVER_URL', 'http://localhost:8761/eureka');
        $appName = strtoupper(env('APP_NAME', 'Laravel')); // Eureka expects uppercase
        $hostName = gethostbyname(gethostname()); // Get real IP
        $appUrl = env('APP_URL', 'http://localhost:8000'); // Extract from .env
        $port = parse_url($appUrl, PHP_URL_PORT) ?? 8000; // Extract port dynamically

        $client = new EurekaClient([
            'eurekaDefaultUrl' => $eurekaUrl,
            'hostName' => $hostName,
            'appName' => $appName,
            'ip' => $hostName,
            'port' => [$port, true], // Dynamic port
            'homePageUrl' => "$appUrl/",
            'statusPageUrl' => "$appUrl/api/info", // Adjusted to API route
            'healthCheckUrl' => "$appUrl/api/health" // Adjusted to API route
        ]);

        $client->register();
        $this->info("Service registered with Eureka as $appName at $hostName:$port");

        // Start sending heartbeats
        $client->start();
    }
}
