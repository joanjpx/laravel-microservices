#!/bin/bash

EUREKA_SERVER="http://localhost:8761/eureka/apps/"  # Change if Eureka is hosted elsewhere
APP_NAME="PRODUCTS-SERVICE"

# Get the actual IP address of the machine
IP_ADDR=$(ipconfig getifaddr en0)  # macOS command to get local IP

INSTANCE_ID="${APP_NAME}:${IP_ADDR}:8001"
HOST_NAME="$IP_ADDR"

JSON_PAYLOAD=$(cat <<EOF
{
    "instance": {
        "instanceId": "$INSTANCE_ID",
        "hostName": "$HOST_NAME",
        "app": "$APP_NAME",
        "ipAddr": "$HOST_NAME",
        "port": { "@enabled": "true", "$": 80 },
        "vipAddress": "$APP_NAME",
        "secureVipAddress": "$APP_NAME",
        "status": "UP",
        "dataCenterInfo": {
            "@class": "com.netflix.appinfo.InstanceInfo\$DefaultDataCenterInfo",
            "name": "MyOwn"
        }
    }
}
EOF
)

echo "Registering in Eureka with IP: $IP_ADDR:${PORT}..."
curl -X POST -H "Content-Type: application/json" -d "$JSON_PAYLOAD" "$EUREKA_SERVER$APP_NAME"
echo "Registration completed!"
