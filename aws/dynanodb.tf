locals {
    hash_key = "pk"
    range_key = "sk"
    ls1_key = "lsi1"
    ls2_key = "lsi2"
    ls3_key = "lsi3"
    ls4_key = "lsi4"
    ls5_key = "lsi5"
}

resource "aws_dynamodb_table" "allcoin_track" {
    hash_key = local.hash_key
    range_key = local.range_key
    name = var.dynamodb_table_name
    read_capacity = 5
    write_capacity = 2

    point_in_time_recovery {
        enabled = true
    }

    attribute {
        name = local.hash_key
        type = "S"
    }

    attribute {
        name = local.range_key
        type = "S"
    }

    local_secondary_index {
        name = local.ls1_key
        projection_type = "ALL"
        range_key = local.ls1_key
    }

    attribute {
        name = local.ls1_key
        type = "S"
    }

    local_secondary_index {
        name = local.ls2_key
        projection_type = "ALL"
        range_key = local.ls2_key
    }

    attribute {
        name = local.ls2_key
        type = "S"
    }

    local_secondary_index {
        name = local.ls3_key
        projection_type = "ALL"
        range_key = local.ls3_key
    }

    attribute {
        name = local.ls3_key
        type = "S"
    }

    local_secondary_index {
        name = local.ls4_key
        projection_type = "ALL"
        range_key = local.ls4_key
    }

    attribute {
        name = local.ls4_key
        type = "N"
    }

    local_secondary_index {
        name = local.ls5_key
        projection_type = "ALL"
        range_key = local.ls5_key
    }

    attribute {
        name = local.ls5_key
        type = "N"
    }
}
