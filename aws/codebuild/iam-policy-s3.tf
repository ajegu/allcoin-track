resource "aws_iam_policy" "policy_allcoin_track_codebuild_s3" {
  name = "${var.app_name}-CodeBuildIAMPolicyS3"

  path = "/"
  description = "IAM policy for S3 from CodeBuild"

  policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": [
        "s3:*"
      ],
      "Resource": "*",
      "Effect": "Allow"
    }
  ]
}
EOF
}

resource "aws_iam_role_policy_attachment" "policy_attachment_allcoin_track_codebuild_s3" {
  role = aws_iam_role.role_allcoin_track_codebuild.name
  policy_arn = aws_iam_policy.policy_allcoin_track_codebuild_s3.arn
}
