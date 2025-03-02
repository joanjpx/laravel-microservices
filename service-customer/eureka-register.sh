#!/bin/bash

EUREKA_SERVER="http://localhost:8761/eureka/apps/"  # Change if Eureka is hosted elsewhere
APP_NAME="CUSTOMERS-SERVICE"
INSTANCE_ID="${APP_NAME}:$(hostname):80"
HOST_NAME=$(hostname)  # macOS-friendly

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

echo "Registrando en Eureka..."
curl -X POST -H "Content-Type: application/json" -d "$JSON_PAYLOAD" "$EUREKA_SERVER$APP_NAME"
echo "Registro completado"
