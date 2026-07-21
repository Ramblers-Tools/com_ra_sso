#!/usr/bin/env bash
# Builds the ra-sso-login installer zip from source.
# Usage: build-package.sh <package-xml-version> <output-zip-path>
set -euo pipefail

VERSION="$1"
mkdir -p "$(dirname "$2")"
OUTPUT="$(cd "$(dirname "$2")" && pwd)/$(basename "$2")"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC="$SCRIPT_DIR"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

build_subzip() {
    local dir="$1" name="$2"
    (cd "$SRC/$dir" && zip -qXr "$WORK/$name" .)
}

build_subzip plg_system_ra_sso plg_system_ra_sso.zip
build_subzip plg_system_ra_sso_error_redirect plg_system_ra_sso_error_redirect.zip
build_subzip plg_webservices_ra_sso plg_webservices_ra_sso.zip
build_subzip com_ra_sso com_ra_sso.zip
build_subzip lib_ra_sso lib_ra_sso.zip

sed "s#<version>[^<]*</version>#<version>${VERSION}</version>#" \
    "$SRC/pkg_oauthclient.xml" > "$WORK/pkg_ra_sso_login.xml"

cp "$SRC/pkg_script.php" "$SRC/LICENSE.txt" "$WORK/"
cp -r "$SRC/language" "$WORK/"

mkdir -p "$(dirname "$OUTPUT")"
rm -f "$OUTPUT"
(cd "$WORK" && zip -qXr "$OUTPUT" .)
