#!/usr/bin/env bash
set -euo pipefail

if [ "$#" -lt 2 ]; then
	echo "Usage: $0 <wp-root> <owner> [group] [webserver-group]" >&2
	exit 1
fi

WP_ROOT=$1
WP_OWNER=$2
WP_GROUP=${3:-$2}
WS_GROUP=${4:-$WP_GROUP}

chown -R "$WP_OWNER:$WP_GROUP" "$WP_ROOT"
find "$WP_ROOT" -type d -exec chmod 755 {} +
find "$WP_ROOT" -type f -exec chmod 644 {} +

if [ -f "$WP_ROOT/wp-config.php" ]; then
	chgrp "$WS_GROUP" "$WP_ROOT/wp-config.php"
	chmod 640 "$WP_ROOT/wp-config.php"
fi

chgrp -R "$WS_GROUP" "$WP_ROOT/wp-content"
find "$WP_ROOT/wp-content" -type d -exec chmod 775 {} +
find "$WP_ROOT/wp-content" -type f -exec chmod 664 {} +
