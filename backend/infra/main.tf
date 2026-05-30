terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.0"
    }
  }

  backend "s3" {
    bucket         = "duskaotearoa-terraform-state-logging-system"
    key            = "logging-system/terraform.tfstate"
    region         = "us-east-1"
    dynamodb_table = "terraform-state-lock"
    encrypt        = true
    profile        = "051826704696_AdministratorAccess"
  }
}

provider "aws" {
  region  = var.aws_region
  profile = "051826704696_AdministratorAccess"
}

# Data source for AWS account ID
data "aws_caller_identity" "current" {}

module "iam" {
  source        = "./modules/iam"
  sqs_queue_arn = module.default_sqs.default_sqs_queue_arn
}

module "default_sqs" {
  source = "./modules/sqs"
  name   = "default_sqs"
  tags = {
    Name = "default_sqs"
  }
}

module "ec2_instance" {
  source          = "./modules/ec2"
  aws_account_id  = data.aws_caller_identity.current.account_id
  stack_name      = var.stack_name
  aws_region      = var.aws_region
  environment     = var.environment
}

module "api_gateway" {
  source = "./modules/api-gateway"
  stack_name = var.stack_name
  environment = var.environment
  stage_name = "prod"
  backend_url = var.backend_url_override != "" ? var.backend_url_override : module.ec2_instance.backend_url
  cloudwatch_log_group_arn = "arn:aws:logs:us-east-1:051826704696:log-group:/aws/apigateway/mos5j2g72f"
}
