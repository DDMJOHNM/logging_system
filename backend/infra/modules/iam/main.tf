# IAM Users
resource "aws_iam_user" "github_actions_deploy_logging_system" {
  name = "github-actions-deploy-logging-system"
  path = "/"

  tags = {
    Name        = "github-actions-deploy-logging-system"
    Environment = var.stack_name
    Purpose     = "GitHub Actions Deployment"
  }
}

resource "aws_iam_user" "logging_system_admin_user" {
  name = "logging-system-admin"
  path = "/"

  tags = {
    Name        = "logging-system-admin"
    Environment = var.stack_name
    Purpose     = "Administrator"
  }
}

# IAM User Group - Cloud_Watch
resource "aws_iam_group" "cloud_watch_logging_system" {
  name = "Cloud_Watch"
  path = "/"
}

# Attach CloudWatch policies to the Cloud_Watch group
resource "aws_iam_group_policy_attachment" "cloud_watch_logging_system_read_only" {
  group      = aws_iam_group.cloud_watch_logging_system.name
  policy_arn = "arn:aws:iam::aws:policy/CloudWatchReadOnlyAccess"
}

resource "aws_iam_group_policy_attachment" "cloud_watch_logs_logging_system_read_only" {
  group      = aws_iam_group.cloud_watch_logging_system.name
  policy_arn = "arn:aws:iam::aws:policy/CloudWatchLogsReadOnlyAccess"
}

# Attach Administrator Access to both users
resource "aws_iam_user_policy_attachment" "logging_system_admin_user_admin" {
  user       = aws_iam_user.logging_system_admin_user.name
  policy_arn = "arn:aws:iam::aws:policy/AdministratorAccess"
}

resource "aws_iam_user_policy_attachment" "github_actions_deploy_logging_system_admin" {
  user       = aws_iam_user.github_actions_deploy_logging_system.name
  policy_arn = "arn:aws:iam::aws:policy/AdministratorAccess"
}

# Add logging_system_admin_user to Cloud_Watch group
resource "aws_iam_user_group_membership" "logging_system_admin_cloud_watch" {
  user = aws_iam_user.logging_system_admin_user.name
  groups = [
    aws_iam_group.cloud_watch_logging_system.name,
  ]
}
