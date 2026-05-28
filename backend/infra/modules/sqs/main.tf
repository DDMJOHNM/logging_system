resource "aws_sqs_queue" "default_sqs" {  
    name = var.name
    tags = {
        Name = var.tags.Name
    }
   
    visibility_timeout_seconds  = 30
    message_retention_seconds   = 86400
    receive_wait_time_seconds   = 10
}

