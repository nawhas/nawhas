# Nawhas.com

## Getting started

```bash
docker-compose up -d
```

## Tips & Tricks

#### Sync S3 Buckets
As a developer on the Nawhas team, it may be necessary to sync the assets in our S3
bucket to your own bucket as needed. Configure the AWS CLI and then use
the command below to quickly synchronize your bucket with staging.

```shell script
aws s3 sync s3://staging.nawhas s3://{your-bucket-here}
```
