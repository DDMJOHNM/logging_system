#!/bin/bash
set -euo pipefail
exec > /var/log/user-data.log 2>&1

echo "Bootstrapping EC2 for Docker Compose deploy (region: ${aws_region})"

yum update -y
yum install -y git

amazon-linux-extras install docker -y 2>/dev/null || yum install -y docker
systemctl enable docker
systemctl start docker
usermod -aG docker ec2-user

mkdir -p /usr/local/lib/docker/cli-plugins
curl -fsSL "https://github.com/docker/compose/releases/download/v2.24.6/docker-compose-linux-x86_64" \
  -o /usr/local/lib/docker/cli-plugins/docker-compose
chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
ln -sf /usr/local/lib/docker/cli-plugins/docker-compose /usr/local/bin/docker-compose

mkdir -p /opt/logging_system
chown ec2-user:ec2-user /opt/logging_system

echo "Bootstrap complete. Clone the repo to /opt/logging_system and add .env.production before first deploy."
