#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  NativePHP Android Build Script${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Create a writable copy of the project
echo -e "${YELLOW}[1/6] Creating writable build directory...${NC}"
BUILD_DIR="/tmp/nativephp-build"
rm -rf "$BUILD_DIR"
cp -r /app "$BUILD_DIR"
cd "$BUILD_DIR"

# Install PHP dependencies
echo -e "${YELLOW}[2/6] Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader --no-interaction

# Install Node dependencies
echo -e "${YELLOW}[3/6] Installing npm dependencies...${NC}"
npm ci --silent

# Build frontend assets
echo -e "${YELLOW}[4/6] Building frontend assets...${NC}"
npm run build

# Install NativePHP for Android
echo -e "${YELLOW}[5/6] Installing NativePHP Android...${NC}"
php artisan native:install android --no-interaction || true

# Build the APK
echo -e "${YELLOW}[6/6] Building Android APK...${NC}"

BUILD_ARGS="--no-tty"

# Check if keystore is configured for release build
if [ -n "$ANDROID_KEYSTORE_FILE" ] && [ -f "$ANDROID_KEYSTORE_FILE" ]; then
    echo -e "${GREEN}Keystore found - building RELEASE APK${NC}"
    BUILD_ARGS="$BUILD_ARGS --keystore=$ANDROID_KEYSTORE_FILE"

    if [ -n "$ANDROID_KEYSTORE_PASSWORD" ]; then
        BUILD_ARGS="$BUILD_ARGS --keystore-password=$ANDROID_KEYSTORE_PASSWORD"
    fi

    if [ -n "$ANDROID_KEY_ALIAS" ]; then
        BUILD_ARGS="$BUILD_ARGS --key-alias=$ANDROID_KEY_ALIAS"
    fi

    if [ -n "$ANDROID_KEY_PASSWORD" ]; then
        BUILD_ARGS="$BUILD_ARGS --key-password=$ANDROID_KEY_PASSWORD"
    fi
else
    echo -e "${YELLOW}No keystore configured - building DEBUG APK${NC}"
fi

php artisan native:package android $BUILD_ARGS

# Copy APK to output directory
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Build Complete!${NC}"
echo -e "${GREEN}========================================${NC}"

# Find and copy the APK
APK_PATH=$(find "$BUILD_DIR/nativephp/android" -name "*.apk" -type f 2>/dev/null | head -1)

if [ -n "$APK_PATH" ]; then
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    APK_NAME="app-${NATIVEPHP_APP_VERSION:-debug}-${TIMESTAMP}.apk"

    cp "$APK_PATH" "/output/$APK_NAME"

    echo ""
    echo -e "${GREEN}APK copied to: /output/$APK_NAME${NC}"
    echo -e "${GREEN}On host: ./build-output/$APK_NAME${NC}"
    echo ""

    # Show APK info
    ls -lh "/output/$APK_NAME"
else
    echo -e "${RED}Warning: Could not find APK file${NC}"
    echo "Searching in build directory..."
    find "$BUILD_DIR" -name "*.apk" -type f 2>/dev/null || echo "No APK files found"
fi

echo ""
echo -e "${BLUE}Done!${NC}"
