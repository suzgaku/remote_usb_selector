#!/bin/sh
out=`ls /tmp/remote_usb_selector-main/install.sh `
out_cut=`echo ${out} | cut -c 1-1 `
if [ "$out_cut" = "/" ]; then
    echo "Remote USB Selector update..."
    cd /tmp/remote_usb_selector-main
    ./install.sh
    cd ..
    rm -rf remote_usb_selector-main
    rm -rf main.zip
    echo "shutdown after 20seconds....."
    sync
    sleep 20
    shutdown -h now 
fi
