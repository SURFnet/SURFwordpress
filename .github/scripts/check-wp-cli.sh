#!/usr/bin/env bash

# Usage: bash .github/scripts/check-wp-cli.sh <REMOTE_USER> <REMOTE_HOST> <REMOTE_PATH> <COMMAND>

REMOTE_USER=$1
REMOTE_HOST=$2
REMOTE_PATH=$3
COMMAND=$4

if ssh -q "$REMOTE_USER@$REMOTE_HOST" "cd $REMOTE_PATH && wp help $COMMAND > /dev/null 2>&1"; then
  echo "$COMMAND exists"
  exit 0
else
  echo "::error::$COMMAND missing"
  exit 11
fi
