resource "aws_codebuild_project" "codebuild_project_allcoin_track" {
  name = var.app_name
  service_role = aws_iam_role.role_allcoin_track_codebuild.arn
  artifacts {
    type = "S3"
    location = aws_s3_bucket.s3_bucket_allcoin_track_artifact.bucket
  }
  cache {
    type = "S3"
    location = aws_s3_bucket.s3_bucket_allcoin_track_cache.bucket
  }
  environment {
    compute_type = "BUILD_GENERAL1_SMALL"
    image = "aws/codebuild/standard:5.0"
    type = "LINUX_CONTAINER"
    image_pull_credentials_type = "CODEBUILD"
  }
  source {
    type = "GITHUB"
    location = "https://github.com/ajegu/allcoin-track"
    report_build_status = true
  }
  source_version = "main"
}

resource "aws_codebuild_webhook" "codebuild_webhook_allcoin_track" {
  project_name = aws_codebuild_project.codebuild_project_allcoin_track.name
  filter_group {
    filter {
      pattern = "PULL_REQUEST_CREATED"
      type = "EVENT"
    }
  }
}