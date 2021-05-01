provider "aws" {
  profile = "default"
  region = "eu-west-3"
}

terraform {
  backend "s3" {
    bucket = "allcoin-track-deployment"
    key = "terraform.tfstate"
    region = "eu-west-3"
  }
}

module "codebuild" {
  source = "./codebuild"
  app_name = var.app_name
}