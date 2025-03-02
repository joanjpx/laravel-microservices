# Microservices Architecture with Laravel, Eureka, and Spring Cloud Gateway

## Overview
This project integrates Laravel microservices with **Spring Cloud Eureka Server** and **Spring Cloud Gateway** for service discovery and API gateway routing.

## Project Structure
- **Eureka Server** (Service Registry) - Handles service discovery.
- **Spring Cloud Gateway** (API Gateway) - Routes requests to registered services.
- **Laravel Microservices**
  - `CUSTOMERS-SERVICE` (Runs on `http://127.0.0.1:8001`)
  - `PRODUCTS-SERVICE` (Runs on `http://127.0.0.1:8002`)

## Prerequisites
- **Java 17+** (for Eureka & Gateway)
- **Maven or Gradle** (for Spring services)
- **PHP 8+ & Composer** (for Laravel services)
- **Laravel Framework 8+**

---

## 🔹 Step 1: Configure Laravel Microservices
Each Laravel microservice must have **EUREKA_SERVER_URL** set in `.env`:

### **CUSTOMERS-SERVICE (.env)**
```env
APP_NAME=CUSTOMERS-SERVICE
APP_URL=http://127.0.0.1:8001
EUREKA_SERVER_URL=http://127.0.0.1:8761/eureka
```

### **PRODUCTS-SERVICE (.env)**
```env
APP_NAME=PRODUCTS-SERVICE
APP_URL=http://127.0.0.1:8002
EUREKA_SERVER_URL=http://127.0.0.1:8761/eureka
```


## Laravel Eureka Registration

Each Laravel microservice has a custom command to register itself with Eureka. Run this command **after the Laravel application starts**:

```sh
php artisan app:eureka-register
```

### Command Implementation (`app/Console/Commands/Eureka.php`)

```php
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
```

Ensure that the following API routes exist in `routes/api.php` for each Laravel service:

```php
Route::get('/info', function () {
    return response()->json(['service' => env('APP_NAME', 'Laravel'), 'status' => 'running']);
});

Route::get('/health', function () {
    return response()->json(['status' => 'UP']);
});
```

Run each Laravel service:

```sh
php artisan serve --port=8001  # Customers Service
php artisan serve --port=8002  # Products Service
```

---

## 🔹 Step 2: Deploy Eureka Server

Clone the Eureka server repository:

```sh
git clone https://github.com/Netflix/eureka.git
cd eureka
```

Run the Eureka server:

```sh
mvn spring-boot:run
```

Eureka should be accessible at: [http://127.0.0.1:8761](http://127.0.0.1:8761)

---

## 🔹 Step 3: Deploy API Gateway

### **Gateway Configuration (`application.yml`)**
Ensure `api-gateway` is configured to route requests to the Laravel microservices:

```yaml
server:
  port: 8000

spring:
  application:
    name: api-gateway
  cloud:
    gateway:
      routes:
        - id: customers-service
          uri: lb://CUSTOMERS-SERVICE
          predicates:
            - Path=/api/customers/**

        - id: products-service
          uri: lb://PRODUCTS-SERVICE
          predicates:
            - Path=/api/products/**

eureka:
  client:
    serviceUrl:
      defaultZone: http://127.0.0.1:8761/eureka/
    registerWithEureka: true
    fetchRegistry: true
  instance:
    preferIpAddress: true
```

Run the API Gateway:

```sh
./mvnw spring-boot:run
```

The gateway should be available at: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 🔹 Step 4: Verify Services in Eureka
Go to [http://127.0.0.1:8761](http://127.0.0.1:8761) and check if **CUSTOMERS-SERVICE** and **PRODUCTS-SERVICE** appear as **UP**.

---

## 🔹 Step 5: Test API Calls via Gateway

```sh
curl http://127.0.0.1:8000/api/customers/info
curl http://127.0.0.1:8000/api/products/info
```

If the services return `"status": "running"`, the setup is complete! 🎉

