#!/usr/bin/env bash

# Usage: bash .github/scripts/development-sync.sh <TEMP_DIR> <SYNC_BRANCH> <CMT_TYPE> <CMT_SCOPE> <CMT_MESSAGE> <JIRA_NO>

set -e

TEMP_DIR=$1
SYNC_BRANCH=$2
CMT_TYPE=$3
CMT_SCOPE=$4
CMT_MESSAGE=$5
JIRA_NO=$6

echo "🚀 Starting sync process for branch: $SYNC_BRANCH"

# Capture the generated message
COMMIT_MSG=$(bash .github/scripts/generate-commit-msg.sh "$CMT_TYPE" "$CMT_SCOPE" "$CMT_MESSAGE" "$JIRA_NO")
EXIT_CODE=$?
if [ $EXIT_CODE -ne 0 ]; then
  echo "::error::Commit generation failed with code: $EXIT_CODE"
  exit $EXIT_CODE
fi

echo "📝 Generated commit message: $COMMIT_MSG"

# 1. Filter & Prepare
if [ -d "$TEMP_DIR" ]; then
  rsync -av \
    --delete \
    --exclude='.git' \
    --exclude='.github/workflows' \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.env' \
    --include='.github/' \
    --include='.github/scripts/***' \
    --include='wp-content/' \
    --include='wp-content/themes/' \
    --include='wp-content/themes/wp-surf-theme/***' \
    --exclude='*' \
    "$TEMP_DIR/" .
else
  echo "::error::Source directory not found!"
  exit 21
fi

rm -rf "$TEMP_DIR"

# 2. Commit and Push
git config user.name "Van Ons (bot)"
git config user.email "info@van-ons.nl"
git checkout -b "$SYNC_BRANCH" 2>/dev/null || git checkout "$SYNC_BRANCH"
git add .
git status

if git diff --cached --quiet; then
  echo "✅ No changes detected."
  SUMMARY_MSG="No changes were detected during the sync process."
  HAS_CHANGES=false

else
  git commit -m "$COMMIT_MSG"
  git push origin "$SYNC_BRANCH" --force
  echo "✨ Changes pushed."
  SUMMARY_MSG="Changes have been successfully pushed to the \`$SYNC_BRANCH\` branch."
  HAS_CHANGES=true
fi

# 3. Create PR Summary link (conditional)
PR_TITLE="$COMMIT_MSG"
PR_BODY="Automatic sync from development repo ($(date +"%Y-%m-%d %H:%M"))---%0A%0A> **Note:** Please ensure this PR is merged using the **Squash and merge** option to maintain a clean history in the $GITHUB_REPOSITORY open source repo."
ASSIGNEE="$GITHUB_ACTOR"
REVIEWER="surf-dev-team"

PR_URL="https://github.com/$GITHUB_REPOSITORY/compare/master...$SYNC_BRANCH?expand=1&title=${PR_TITLE// /%20}&body=${PR_BODY// /%20}&assignee=$ASSIGNEE&reviewers=$REVIEWER"

{
  echo "## 🚀 Sync Completed"
  echo "$SUMMARY_MSG"

  # Add PR section ONLY when there are changes to commit
  if [ "$HAS_CHANGES" = true ]; then
    echo ""
    echo "---"
    echo "✅ **Assignee:** $ASSIGNEE"
    echo "✅ **Reviewer:** $REVIEWER"
    echo ""
    echo "👉 [Click here to create the Pull Request]($PR_URL)"
  fi
} >> "$GITHUB_STEP_SUMMARY"
