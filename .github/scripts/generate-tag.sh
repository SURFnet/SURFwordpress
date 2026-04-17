#!/usr/bin/env bash

# Generates a release tag based on current year and existing tags
# Output: vYYYY.X

MODE=$1 # "stable" or "beta"

YEAR=$(date +'%Y')

BASE_COUNT=$(git tag --list "v${YEAR}.*" | grep -v beta | wc -l)
BASE_COUNT=$((BASE_COUNT + 1))

if [[ "$MODE" == "beta" ]]; then
  BETA_COUNT=$(git tag --list "v${YEAR}.${BASE_COUNT}-beta.*" | wc -l)
  BETA_COUNT=$((BETA_COUNT + 1))
  echo "v${YEAR}.${BASE_COUNT}-beta.${BETA_COUNT}"
else
  echo "v${YEAR}.${BASE_COUNT}"
fi
