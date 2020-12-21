#!/bin/sh
if [ "$update_flag" = 1 ]; then
    echo "Remote USB Selector update..."
    cd /tmp/remote_usb_selector-main
    ./install.sh
    cd ..
    rm -rf remote_usb_selector-main
    rm -rf main.zip
    echo "shutdown after 20seconds."
    sync
    sleep 20
    shutdown -h now 
fi
