#!/usr/bin/python

from scapy.all import *
import sys
import os
import time
from random import randint

flag="./flag"
client_ip=os.environ["SOCAT_PEERADDR"]


print >> sys.stderr, client_ip
    
def failed():
    print_message ("You're not one of us!")
    sys.exit(0);

def print_message(msg):
    print msg+"\n"
    sys.stdout.flush()

def send_flag():
    print_message ("Welcome back, brother...")
    with open (flag) as f:
        print f.readlines()[0];
    sys.exit(0);
    

def poke_me(dport):
    rand_port=randint(1024,65535)

    port_start=9100
    port_end=9200

    port1=randint(port_start,port_end)
    port2=randint(port_start,port_end)
    port3=randint(port_start,port_end)

    port_arr=[port1, port2, port3]

    print_message("Knock Knock!!")
    payload="Poke me on ports "+str(port_arr[0])+" "+str(port_arr[1])+" "+str(port_arr[2])
    a=IP(dst=client_ip)/TCP(sport=rand_port,dport=dport)/payload
    send(a, verbose=0)

    for p in port_arr:
        # Filter= is returning random packets on some machines. Going for lfilters
        # filter="not port 5001 and ip host "+client_ip+" and tcp port "+str(p), 
        build_lfilter = lambda (r): TCP in r and r[TCP].dport == p and r[IP].src == client_ip
        p1=sniff(lfilter=build_lfilter , count=1, timeout=2, iface="eth0")
        if (p1):
            print_message("humhum...");
        else:
            failed()
    print_message ("Rock & Roll!");
    
    

def open_the_window(dport):
    print_message ("Open the window!")
    sport=randint(1024,65535)
    window=IP(dst=client_ip)/TCP(sport=sport,dport=dport)/"Open it to the maximum!"
    resp=sr1(window, verbose=0, timeout=2)
    if (resp):
        w=resp[TCP].window
        if (w == 65535):
            print_message("Good...;)")
        else:
            failed()
    else:
        failed()


def ask_urgent(dport):
    print_message ("Reply urgently!");
    sport=randint(1024,65535)
    urgent=IP(dst=client_ip)/TCP(sport=sport,dport=dport)/"Do you know the Urgent flag?"
    resp=sr1(urgent, verbose=0, timeout=2)
    if (resp):
        resp.show
        flags=resp[TCP].flags
        urg_flag=flags & int('0b000000100000',2)
        if (urg_flag == 0):
            failed()
        else:
            print_message("Good...I like to talk with you!");
    else:
        failed()
    
    
print_message ("Are you listening?")

ask_urgent(3000)
time.sleep(1)
open_the_window(3001)
time.sleep(1)
poke_me(3002)

send_flag()
