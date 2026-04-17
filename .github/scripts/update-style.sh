#!/usr/bin/env bash

# Replaces placeholders in a given theme's style.css
# Usage: bash .github/scripts/update-style.sh THEME_PATH THEME_VERSION PHP_VERSION

THEME_PATH=$1
THEME_VERSION=$2
PHP_VERSION=$3
YEAR=$(date +'%Y')

if [[ ! -d "$THEME_PATH" ]]; then
  echo "Theme path $THEME_PATH does not exist"
  exit 1
fi

if [[ ! -f "$THEME_PATH/style.css" ]]; then
  echo "Error: $THEME_PATH/style.css does not exist"
  exit 1
fi

SED_INPLACE="sed -i"
if [[ "$(uname)" == "Darwin" ]]; then
  SED_INPLACE="sed -i ''"
fi

echo "Replacing placeholders in $THEME_PATH/style.css:"
echo " - THEME_VERSION -> $THEME_VERSION"
echo " - PHP_VERSION -> $PHP_VERSION"
echo " - YEAR -> $YEAR"

$SED_INPLACE "s|\[THEME_VERSION\]|$THEME_VERSION|" "$THEME_PATH/style.css"
$SED_INPLACE "s|\[PHP_VERSION\]|$PHP_VERSION|" "$THEME_PATH/style.css"
$SED_INPLACE "s|\[YEAR\]|$YEAR|" "$THEME_PATH/style.css"
