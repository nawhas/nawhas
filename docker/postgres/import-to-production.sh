#!/usr/bin/env sh
set -ex

FILENAME=$1

if [ -z "$FILENAME" ]
then
  echo "Usage: $0 {dump-XXXXXXXXX.sql}"
  exit 1;
fi

DUMP="/opt/$1"

if [ ! -f "$DUMP" ]
then
  echo "Dump file $DUMP does not exist.";
  exit 1;
fi

echo "Importing $DUMP to production database.";

pg_restore -d "$PROD_CONNECTION" \
  --no-privileges \
  --no-acl \
  --no-owner \
  --jobs 4 \
  --schema=public \
  "$DUMP";

echo "Done!";
