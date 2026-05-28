variable "name" {
    default = "default_sqs"
    description = "The name of the SQS queue"
    type = string
}

variable "tags" {
    default = {
        Name = "default_sqs"
        Environment = "production"    
    }    
    description = "The tags to apply to the SQS queue"
    type = map(string)
}