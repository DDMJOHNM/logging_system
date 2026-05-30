output "github_actions_user_arn" {
  description = "ARN of the github-actions-deploy user"
  value       = module.iam.github_actions_user_arn
}

output "john_mason_user_arn" {
  description = "ARN of the logging-system-admin"
  value       = module.iam.logging_system_admin_user_arn
}

output "cloud_watch_group_name" {
  description = "Name of the Cloud_Watch IAM group"
  value       = module.iam.cloud_watch_group_name
}

output "cloud_watch_group_arn" {
  description = "ARN of the Cloud_Watch IAM group"
  value       = module.iam.cloud_watch_group_arn
}

output "ec2_instance_id" {
  description = "EC2 backend instance ID"
  value       = module.ec2_instance.instance_id
}

output "ec2_public_ip" {
  description = "EC2 backend public IP"
  value       = module.ec2_instance.public_ip
}

output "sqs_queue_url" {
  description = "SQS queue URL for OBSERVABILITY_SQS_QUEUE_URL"
  value       = module.default_sqs.default_sqs_queue_url
}
