#!/usr/bin/env bash

# Usage: bash generate-tag.sh [stable|beta]
# Generates a release tag based on the current year and existing tags.
# Format: vYYYY.X (stable) or vYYYY.X-beta.N (beta)

MODE=${1:-"stable"}
YEAR=$(date +'%Y')

# 1. Find the latest stable version
LAST_STABLE=$(git tag -l "v${YEAR}.*" | grep -v beta | sort -V | tail -n1)
BASE_VERSION=$(echo "$LAST_STABLE" | cut -d. -f2)
BASE_VERSION=${BASE_VERSION:-0}

# The upcoming stable version is always BASE + 1
NEXT_STABLE=$((BASE_VERSION + 1))

if [[ "$MODE" == "beta" ]]; then
  # 2. Look for existing betas for the NEXT stable version
  LAST_BETA=$(git tag -l "v${YEAR}.${NEXT_STABLE}-beta.*" | sort -V | tail -n1)

  if [[ -z "$LAST_BETA" ]]; then
    # No beta yet for this upcoming version, start at .1
    echo "v${YEAR}.${NEXT_STABLE}-beta.1"
  else
    # Increment the beta counter
    BETA_COUNT=$(echo "$LAST_BETA" | cut -d. -f3)
    NEW_BETA=$((BETA_COUNT + 1))
    echo "v${YEAR}.${NEXT_STABLE}-beta.${NEW_BETA}"
  fi

else
  # Stable mode: simply release the next version
  echo "v${YEAR}.${NEXT_STABLE}"
fi
