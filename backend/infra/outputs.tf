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
