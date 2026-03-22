#!/bin/sh

TARGET_DIR="/var/www/html/public/assets/images"
SOURCE_FILE="/tmp/default.png"

echo "Checking default image..."
mkdir -p "$TARGET_DIR"

if [ ! -f "$SOURCE_FILE" ]; then
    echo "ERROR: Source file $SOURCE_FILE not found!"
else
    if [ ! -f "$TARGET_DIR/default.png" ]; then
        echo "Copying default image..."
        cp "$SOURCE_FILE" "$TARGET_DIR/default.png"
    else
        echo "Default image already exists."
    fi
fi

exec apache2-foreground