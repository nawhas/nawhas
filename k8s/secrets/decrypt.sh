#!/bin/sh

# --batch to prevent interactive command --yes to assume "yes" for questions
gpg --quiet --batch --yes --decrypt --passphrase="$GPG_KEY" \
--output api.env api.env.gpg

gpg --quiet --batch --yes --decrypt --passphrase="$GPG_KEY" \
--output api.staging.env api.staging.env.gpg
