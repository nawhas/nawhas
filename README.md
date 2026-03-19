# Nawhas.com

Nawhas is a Docker-based monorepo with:
- `nuxt/` for the web frontend
- `api/` for the Laravel backend

## Prerequisites

- Docker Engine + Docker Compose
- Bash
- AWS CLI (optional, for asset sync tasks)

## Quick Start

```bash
# start the local stack
./dev up -d

# check service status
./dev ps
```

By default, `./dev` uses `docker-compose.dev.yml` and automatically includes
`docker-compose.override.yml` when present.

## Common Development Commands

```bash
# open a shell in the API container
./dev bash

# run Laravel artisan commands
./dev art migrate:all

# run Composer in the API container
./dev composer install

# run Yarn in the web container
./dev yarn install
```

## Testing and Linting

```bash
# run PHP linters (phpstan + psalm)
./dev lint:php

# run test suites
./dev test:unit
./dev test:feature

# run all checks
./dev test
```

## TLS Certificates for Local Domains

Generate local certs used by the nginx setup:

```bash
./dev certs
```

## Tips & Tricks

### Sync S3 Buckets

To copy assets from staging into your own bucket:

```bash
aws s3 sync s3://staging.nawhas s3://{your-bucket-here}
```

### Creating New Databases

1. Create a new database in DigitalOcean.
2. Open a shell and grant permissions:

```bash
./dev exec db sh
psql {{connection-string}}
GRANT ALL PRIVILEGES ON DATABASE {{new_database}} TO "{{user}}";
```
