#!/usr/bin/env sh

set -e

DUMP=/opt/dump.sql
echo "Dumping database from staging to $DUMP.";

PGPASSWORD="$STG_DB_PASS" pg_dump \
  --no-password \
  --no-privileges \
  --clean \
  --if-exists \
  --no-acl \
  --no-owner \
  -h "$STG_DB_HOST" \
  -U "$STG_DB_USER" \
  --schema=public \
  --table _doctrine \
  --table reciters \
  --table albums \
  --table tracks \
  --table lyrics \
  --table media \
  --table track_media \
  --table reciter_visits \
  --table track_visits \
  -f "$DUMP" \
  -Fc \
  "$POSTGRES_DB";

psql -U "$POSTGRES_USER" -d postgres -c "SELECT pg_terminate_backend(pg_stat_activity.pid) FROM pg_stat_activity WHERE pg_stat_activity.datname = '$POSTGRES_DB' AND pid <> pg_backend_pid();" > /dev/null

psql -U "$POSTGRES_USER" -d postgres -c "DROP DATABASE $POSTGRES_DB;" > /dev/null

psql -U "$POSTGRES_USER" -d postgres -c "CREATE DATABASE $POSTGRES_DB;" > /dev/null

PGPASSWORD="$POSTGRES_PASSWORD" pg_restore \
  --no-privileges \
  --clean \
  --if-exists \
  --no-acl \
  --no-owner \
  -U "$POSTGRES_USER" \
  --schema=public \
  -d "$POSTGRES_DB" \
  "$DUMP";

echo "Done!";
