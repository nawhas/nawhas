#!/usr/bin/env sh
exit 1; # DO NOT RUN THIS COMMAND
set -ex

FILENAME=$1

if [ -z "$FILENAME" ]
then
  echo "Usage: $0 {dump-XXXXXXXXX.sql}"
  exit 1;
fi

DUMP="/opt/$1"
POSTGRES_DB=nawhas_production

if [ ! -f "$DUMP" ]
then
  echo "Dump file $DUMP does not exist.";
  exit 1;
fi

echo "Importing $DUMP to production database.";

#psql -U "$POSTGRES_USER" -d postgres -c "SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '$POSTGRES_DB' AND pid <> pg_backend_pid();" > /dev/null

psql -U "$POSTGRES_USER" -d postgres -c "DROP DATABASE $POSTGRES_DB;"
psql -U "$POSTGRES_USER" -d postgres -c "CREATE DATABASE $POSTGRES_DB;"

pg_restore -d "$PROD_CONN_STR" \
  --no-privileges \
  --clean \
  --if-exists \
  --no-acl \
  --no-owner \
  --jobs 4 \
  --schema=public \
  "$DUMP";

echo "Done!";
