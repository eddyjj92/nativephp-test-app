# NativePHP Android Build with Docker

Build your NativePHP app for Android using Docker on any VPS or CI/CD server.

## Requirements

- Docker Engine 20.10+
- Docker Compose v2+
- ~10GB disk space (for Android SDK cache)

## Quick Start

### 1. Build the Docker image

```bash
docker compose build
```

### 2. Build a DEBUG APK

```bash
docker compose run --rm build
```

The APK will be saved to `./build-output/`

### 3. Build a RELEASE APK (signed)

First, generate a keystore (one-time):

```bash
keytool -genkey -v -keystore ./keystore/release.keystore \
  -alias my-app-key -keyalg RSA -keysize 2048 -validity 10000
```

Then configure your `.env`:

```env
NATIVEPHP_APP_ID=com.yourcompany.yourapp
NATIVEPHP_APP_VERSION=1.0.0
NATIVEPHP_APP_VERSION_CODE=1

ANDROID_KEYSTORE_FILE=/keystore/release.keystore
ANDROID_KEYSTORE_PASSWORD=your-keystore-password
ANDROID_KEY_ALIAS=my-app-key
ANDROID_KEY_PASSWORD=your-key-password
```

Then build:

```bash
docker compose run --rm build
```

## Commands

| Command | Description |
|---------|-------------|
| `docker compose build` | Build the Docker image |
| `docker compose run --rm build` | Build the APK |
| `docker compose run --rm shell` | Open interactive shell for debugging |

## Directory Structure

```
.
├── docker/
│   ├── build-android.sh    # Build script
│   └── README.md           # This file
├── build-output/           # APK output directory (git-ignored)
├── keystore/               # Keystore files (git-ignored)
├── Dockerfile              # Build environment
└── docker-compose.yml      # Docker Compose config
```

## CI/CD Integration

Example GitHub Actions workflow:

```yaml
name: Build Android APK

on:
  push:
    tags:
      - 'v*'

jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - name: Build APK
        env:
          NATIVEPHP_APP_ID: ${{ vars.NATIVEPHP_APP_ID }}
          NATIVEPHP_APP_VERSION: ${{ github.ref_name }}
          ANDROID_KEYSTORE_PASSWORD: ${{ secrets.ANDROID_KEYSTORE_PASSWORD }}
          ANDROID_KEY_PASSWORD: ${{ secrets.ANDROID_KEY_PASSWORD }}
        run: |
          echo "${{ secrets.ANDROID_KEYSTORE_BASE64 }}" | base64 -d > ./keystore/release.keystore
          docker compose run --rm build

      - name: Upload APK
        uses: actions/upload-artifact@v4
        with:
          name: android-apk
          path: build-output/*.apk
```

## Troubleshooting

### Out of memory during build

Increase Docker memory limit to at least 4GB.

### Gradle errors

Try clearing the Gradle cache:

```bash
docker volume rm nativephp-test-app_gradle-cache
```

### Permission errors

Ensure the build script is executable:

```bash
chmod +x docker/build-android.sh
```
