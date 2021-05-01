resource "aws_iam_policy" "policy_allcoin_track_codebuild_logs" {
  name = "${var.app_name}-CodeBuildIAMPolicyLogs"

  path = "/"
  description = "IAM policy for logging from CodeBuild"

  policy = <<EOF
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Action": [
        "logs:CreateLogGroup",
        "logs:CreateLogStream",
        "logs:PutLogEvents"
      ],
      "Resource": "arn:aws:logs:*:*:*",
      "Effect": "Allow"
    }
  ]
}
EOF
}

resource "aws_iam_role_policy_attachment" "policy_attachment_allcoin_track_codebuild_logs" {
  role = aws_iam_role.role_allcoin_track_codebuild.name
  policy_arn = aws_iam_policy.policy_allcoin_track_codebuild_logs.arn
}
