# NativePHP Android Build Environment
# Based on Ubuntu 22.04 with PHP 8.2, Node.js 20, Java 17, and Android SDK

FROM ubuntu:22.04

LABEL maintainer="NativePHP Builder"
LABEL description="Docker image for building NativePHP Android applications"

# Prevent interactive prompts during package installation
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

# Android SDK configuration
ENV ANDROID_HOME=/opt/android-sdk
ENV ANDROID_SDK_ROOT=/opt/android-sdk
ENV PATH="${PATH}:${ANDROID_HOME}/cmdline-tools/latest/bin:${ANDROID_HOME}/platform-tools:${ANDROID_HOME}/build-tools/34.0.0"

# Java configuration
ENV JAVA_HOME=/usr/lib/jvm/java-17-openjdk-amd64

# ============================================
# Install base dependencies
# ============================================
RUN apt-get update && apt-get install -y \
    curl \
    wget \
    unzip \
    git \
    zip \
    ca-certificates \
    gnupg \
    software-properties-common \
    && rm -rf /var/lib/apt/lists/*

# ============================================
# Install Java 17 (OpenJDK)
# ============================================
RUN apt-get update && apt-get install -y \
    openjdk-17-jdk \
    && rm -rf /var/lib/apt/lists/*

# ============================================
# Install PHP 8.2 with required extensions
# ============================================
RUN add-apt-repository ppa:ondrej/php -y \
    && apt-get update \
    && apt-get install -y \
    php8.2-cli \
    php8.2-common \
    php8.2-curl \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-zip \
    php8.2-bcmath \
    php8.2-intl \
    php8.2-sqlite3 \
    php8.2-gd \
    php8.2-tokenizer \
    && rm -rf /var/lib/apt/lists/*

# ============================================
# Install Composer
# ============================================
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ============================================
# Install Node.js 20 LTS
# ============================================
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# ============================================
# Install Android SDK Command Line Tools
# ============================================
RUN mkdir -p ${ANDROID_HOME}/cmdline-tools \
    && cd ${ANDROID_HOME}/cmdline-tools \
    && wget -q https://dl.google.com/android/repository/commandlinetools-linux-11076708_latest.zip -O cmdline-tools.zip \
    && unzip -q cmdline-tools.zip \
    && rm cmdline-tools.zip \
    && mv cmdline-tools latest

# Accept Android SDK licenses
RUN yes | sdkmanager --licenses > /dev/null 2>&1 || true

# Install Android SDK components
RUN sdkmanager --update \
    && sdkmanager \
    "platform-tools" \
    "platforms;android-34" \
    "build-tools;34.0.0" \
    "extras;android;m2repository" \
    "extras;google;m2repository"

# ============================================
# Create non-root user for builds
# ============================================
RUN groupadd -g 1000 builder \
    && useradd -u 1000 -g builder -m -s /bin/bash builder \
    && chown -R builder:builder ${ANDROID_HOME}

# ============================================
# Set up working directory
# ============================================
WORKDIR /app

# Create output directory
RUN mkdir -p /output && chown builder:builder /output

# Switch to non-root user
USER builder

# Default command
CMD ["bash"]
