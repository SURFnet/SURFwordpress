#!/usr/bin/env bash

# Usage: bash .github/scripts/generate-commit-msg.sh <type> <scope> <message> <jira>
# Validates input and formats a Conventional Commit message.

TYPE=$(echo "${1:-chore}" | tr '[:upper:]' '[:lower:]')
SCOPE=$2
MSG=$3
JIRA=$4

# 1. Validate Message: Ensure it's not empty and not the default text
if [[ -z "$MSG" || "$MSG" == "Update from development repo" ]]; then
  echo "::error::A valid commit message is required."
  exit 31
fi

# 2. Validate JIRA format (if provided)
if [[ -n "$JIRA" ]]; then
  JIRA=$(echo "$JIRA" | tr '[:lower:]' '[:upper:]')
  if [[ ! "$JIRA" =~ ^[A-Z]+-[0-9]+$ ]]; then
    echo "::error::Invalid JIRA format. Use format like SURF-123."
    exit 32
  fi
fi

# 3. Build the commit message
if [[ -n "$SCOPE" ]]; then
  COMMIT_MSG="${TYPE}(${SCOPE}): ${MSG}"
else
  COMMIT_MSG="${TYPE}: ${MSG}"
fi

# 4. Append JIRA if present
if [[ -n "$JIRA" ]]; then
  COMMIT_MSG="${COMMIT_MSG} (${JIRA})"
fi

echo "$COMMIT_MSG"
