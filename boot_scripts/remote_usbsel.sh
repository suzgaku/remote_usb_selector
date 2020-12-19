#!/bin/sh

echo "Remote USB Selector auto setting..."

echo 5 > /sys/class/gpio/export
echo 6 > /sys/class/gpio/export

echo out > /sys/class/gpio/gpio5/direction
echo out > /sys/class/gpio/gpio6/direction

# USB#0に切り替え
echo 0 > /sys/class/gpio/gpio6/value
echo 1 > /sys/class/gpio/gpio5/value

shut_flag=0

# USBメモリ(/dev/sda1)が見つかるまで待ち。ない場合は終了。
echo "Detecting USB-memory..."
for i in `seq 1 10`
do
    out=`ls /dev/sda1 `
    if [ "$out" = "/dev/sda1" ]; then
        echo "Succes."
        break
    fi
    echo "wait"
    sleep 1
done
if [ "${i}" = 20 ]; then
    echo "Not detect."
    exit 1
fi

# オリジナルのWIFI設定ファイルを保存
out=`ls /etc/wpa_supplicant/org_wpa_supplicant.conf `
out_cut=`echo ${out} | cut -c 1-1 `
if [ "$out_cut" != "/" ]; then
    echo "Copying original WIFI file."
    cp /etc/wpa_supplicant/wpa_supplicant.conf /etc/wpa_supplicant/org_wpa_supplicant.conf
fi

# USBメモリをmount
mount /dev/sda1 /sda1 

# USBメモリに rus_wifi.txt がある場合は、wifi設定をする
out=`ls /sda1/rus_wifi.txt `
out_cut=`echo ${out} | cut -c 1-1 `
if [ "$out_cut" = "/" ]; then
    echo "Setting WIFI file from USB-memory."
    \cp -f /etc/wpa_supplicant/org_wpa_supplicant.conf /etc/wpa_supplicant/wpa_supplicant.conf
    cat /sda1/rus_wifi.txt >> /etc/wpa_supplicant/wpa_supplicant.conf
    mv /sda1/rus_wifi.txt /sda1/_rus_wifi.txt
    shut_flag=1
fi

# USBメモリに rus_hostname.txt がある場合は、host名を変更する
out=`ls /sda1/rus_hostname.txt `
out_cut=`echo ${out} | cut -c 1-1 `
if [ "$out_cut" = "/" ]; then
    echo "Setting Hostname from USB-memory."
    out=`head -n 1 /sda1/rus_hostname.txt `
    if [ "${#out}" -gt 3 ]; then
        raspi-config nonint do_hostname ${out}
        mv /sda1/rus_hostname.txt /sda1/_rus_hostname.txt
        shut_flag=1
    fi
fi

# USBメモリに rus_ifconfig.txt がある場合は、ifconfigの結果を格納する
out=`ls /sda1/rus_ifconfig.txt `
out_cut=`echo ${out} | cut -c 1-1 `
if [ "$out_cut" = "/" ]; then
    echo "Writing ifconfig to USB-memory."
    ifconfig > /sda1/rus_ifconfig.txt
    mv /sda1/rus_ifconfig.txt /sda1/_rus_ifconfig.txt
    shut_flag=1
fi

# USBメモリをumount
umount /dev/sda1

if [ "$shut_flag" = 1 ]; then
    echo "shutdown after 20seconds."
    sync
    sleep 20
    shutdown -h now 
    exit 1
fi

# USB#1に切り替え
sleep 1
echo 0 > /sys/class/gpio/gpio5/value
sleep 1
echo 1 > /sys/class/gpio/gpio6/value
echo 1 > /sys/class/gpio/gpio5/value

exit 0
