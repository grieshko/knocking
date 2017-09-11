#!/usr/bin/env python2.7
from __future__ import division
import sqlite3, requests, json
import sys, os, re, time, socket, nmap, msfrpc
from netaddr import *

############################################################################
# SETTINGS
############################################################################
metasploit_workspace = "Raptor"
metasploit_command =
"""
workspace """ +str(metasploit_workspace)+"""
<COMMAND TO PASTE>
"""
############################################################################
client = msfrpc.Msfrpc({})
client.login('msf', 'abc123')
ress = client.call('console.create')
console_id = ress['id']
client.call('console.write',[console_id, commands])
res = client.call('console.read',[console_id])
print res
