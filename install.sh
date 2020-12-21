#!/bin/sh

echo "installing files for remote usb selector..."

\cp -f boot_scripts/remote_usbsel.sh /usr/local/bin/
\cp -f boot_scripts/remote_usbsel_update.sh /usr/local/bin/
\cp -rf html /var/www/

echo "finished."
