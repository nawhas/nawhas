#!/bin/sh

set -x && \
  gpg --symmetric --cipher-algo AES256 api.env
