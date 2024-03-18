#!/bin/sh

OUTPUT=$(vendor/bin/phpstan analyze --no-progress --error-format=github);
STATUS=$?;
echo "$OUTPUT" | sed "s/file=app/file=api\/app/g";

exit $STATUS;
