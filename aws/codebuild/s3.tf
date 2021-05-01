resource "aws_s3_bucket" "s3_bucket_allcoin_track_artifact" {
  bucket = "allcoin-track-artifact"
}

resource "aws_s3_bucket" "s3_bucket_allcoin_track_cache" {
  bucket = "allcoin-track-cache"
}