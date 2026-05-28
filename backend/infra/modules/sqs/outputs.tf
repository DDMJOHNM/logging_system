output "default_sqs_queue_id" {
  description = "The URL for the created Amazon SQS queue"
  value       = aws_sqs_queue.default_sqs.id
}

output "default_sqs_queue_url" {
  description = "URL of the default SQS queue"
  value       = aws_sqs_queue.default_sqs.url
}

output "default_sqs_queue_arn" {
  description = "ARN of the default SQS queue"
  value       = aws_sqs_queue.default_sqs.arn
}

output "default_sqs_queue_name" {
  description = "Name of the default SQS queue"
  value       = aws_sqs_queue.default_sqs.name
}

