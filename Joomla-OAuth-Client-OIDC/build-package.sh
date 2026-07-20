#!/usr/bin/env bash
# Builds the ra-sso-login installer zip from source.
# Usage: build-package.sh <package-xml-version> <output-zip-path>
set -euo pipefail

VERSION="$1"
mkdir -p "$(dirname "$2")"
OUTPUT="$(cd "$(dirname "$2")" && pwd)/$(basename "$2")"

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SRC="$SCRIPT_DIR/miniorange-joomla-oauth-client-free-plugin"
WORK="$(mktemp -d)"
trap 'rm -rf "$WORK"' EXIT

build_subzip() {
    local dir="$1" name="$2"
    (cd "$SRC/$dir" && zip -qXr "$WORK/$name" .)
}

build_subzip plg_system_miniorangeoauth plg_system_miniorangeoauth.zip
build_subzip plg_system_mooautherrorredirect plg_system_mooautherrorredirect.zip
build_subzip plg_webservices_miniorangeoauthclient plg_webservices_miniorangeoauthclient.zip
build_subzip com_miniorange_oauth com_miniorange_oauth.zip
build_subzip lib_miniorangeoauthplugin lib_miniorangeoauthplugin.zip

sed "s#<version>[^<]*</version>#<version>${VERSION}</version>#" \
    "$SRC/pkg_oauthclient.xml" > "$WORK/pkg_ra_sso_login.xml"

cp "$SRC/pkg_script.php" "$SRC/LICENSE.txt" "$WORK/"
cp -r "$SRC/language" "$WORK/"

mkdir -p "$(dirname "$OUTPUT")"
rm -f "$OUTPUT"
(cd "$WORK" && zip -qXr "$OUTPUT" .)
