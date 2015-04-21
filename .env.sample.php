<?php

return array(
    'ENCRYPTION_KEY'           => 'super-secret-sauce',
    'PGSQL_HOST'               => 'localhost',
    'PGSQL_DATABASE'           => 'predictry_db',
    'PGSQL_USERNAME'           => 'predictry_user',
    'PGSQL_PASSWORD'           => 'predictry_password',
    "URL"                      => "http://dashboard.predictry.dev",
    "SMTP_HOST"                => "smtp.mandrillapp.com",
    "SMTP_PORT"                => 587,
    "SMTP_USERNAME"            => "smtp_username",
    "SMTP_PASSWORD"            => "smtp_password",
    "SMTP_GLOBAL_EMAIL_SENDER" => "no-reply@predictry.com",
    "SMTP_GLOBAL_EMAIL_NAME"   => "Recommendation engine powered by Predictry",
    "DEBUG_MODE"               => false,
    "SQS_QUEUE_KEY"            => "aws_key",
    "SQS_QUEUE_SECRET"         => "aws_secret",
    "SQS_QUEUE_URL"            => "aws_url",
    "SQS_QUEUE_REGION"         => "aws_region",
    "TAPIRUS_API_URL"          => "http://tapirus-lb-sg-788431201.ap-southeast-1.elb.amazonaws.com/predictry/api/v1/",
    "TAPIRUS_HTTP_USERNAME"    => "username",
    "TAPIRUS_HTTP_PASSWORD"    => "password",
    "FRONTEND_SKINS"           => "frontend.themes.",
    "PREDICTRY_ANALYTICS_URL"  => "http://predictry-analytics.url/api/v1/"
);
