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
  source = "./modules/iam"
}
 
