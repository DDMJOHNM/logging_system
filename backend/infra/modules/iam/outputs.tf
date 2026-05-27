output "github_actions_user_arn" {
  description = "ARN of the github-actions-deploy user"
  value       = aws_iam_user.github_actions_deploy_logging_system.arn
}

output "logging_system_admin_user_arn" {
  description = "ARN of the logging-system-admin user"
  value       = aws_iam_user.logging_system_admin_user.arn
}

output "cloud_watch_group_name" {
  description = "Name of the Cloud_Watch IAM group"
  value       = aws_iam_group.cloud_watch_logging_system.name
}

output "cloud_watch_group_arn" {
  description = "ARN of the Cloud_Watch IAM group"
  value       = aws_iam_group.cloud_watch_logging_system.arn
}
