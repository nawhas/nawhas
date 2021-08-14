#!/bin/sh

set -x;

gpg --symmetric --cipher-algo AES256 ../overlays/production/secrets/api.env;
gpg --symmetric --cipher-algo AES256 ../overlays/staging/secrets/api.staging.env;
gpg --symmetric --cipher-algo AES256 ../overlays/integration/secrets/api.integration.env;
