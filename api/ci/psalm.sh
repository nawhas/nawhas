#!/bin/sh

OUTPUT=$(vendor/bin/psalm --error-format=github);
STATUS=$?;
echo "$OUTPUT" | sed "s/file=app/file=api\/app/g";

exit $STATUS;
