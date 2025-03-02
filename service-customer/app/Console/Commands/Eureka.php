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
        $eurekaUrl = env('EUREKA_SERVER_URL', 'http://discovery-service:8761/eureka'); // Use correct service name

        $appName = strtoupper(env('APP_NAME', 'Laravel')); // Eureka expects uppercase

        // Use Docker's container name as the hostname
        $hostName = gethostname();

        $appUrl = env('APP_URL', "http://$hostName:8001"); // Ensure correct container name
        $port = parse_url($appUrl, PHP_URL_PORT) ?? 8001; // Set the correct service port

        $client = new EurekaClient([
            'eurekaDefaultUrl' => $eurekaUrl,
            'hostName' => $hostName,  // Now using the Docker container name
            'appName' => $appName,
            'ip' => $hostName, // Eureka will resolve it within the Docker network
            'port' => [$port, true],
            'homePageUrl' => "$appUrl/",
            'statusPageUrl' => "$appUrl/api/info",
            'healthCheckUrl' => "$appUrl/api/health"
        ]);

        $client->register();
        $this->info("Service registered with Eureka as $appName at $hostName:$port");

        // Start sending heartbeats
        $client->start();
    }
}
