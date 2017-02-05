#!/bin/bash

PORT=5001

iptables -F
iptables -A OUTPUT -p tcp --destination-port 3000 --tcp-flags RST RST  -j DROP
socat tcp-listen:${PORT},fork,tcpwrap=script,sndbuf=0,rcvbuf=1 EXEC:./netchat.py
