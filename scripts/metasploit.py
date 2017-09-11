#!/usr/bin/env python2.7
from __future__ import division
import sqlite3, requests, json
import sys, os, re, time, socket, nmap, msfrpc
from netaddr import *

############################################################################
# SETTINGS
############################################################################
databaseFileLocation = "SQLITE DATABASE PATH"
nmap_output_file_path = "NMAP OUTPUT DIRECTORY"
metasploit_workspace = "METASPLOIT WORKSPACE NAME"
############################################################################
class database():
    def sqliteConnect(self):
        databasefile = databaseFileLocation
        conn = sqlite3.connect(databasefile)
        cur = conn.cursor()
        return cur, conn

    def sqlLiteCommit(self, conn):
        conn.commit()
        #conn.close()

#FOR NMAP
client = msfrpc.Msfrpc({})
client.login('msf', 'abc123')
ress = client.call('console.create')
console_id = ress['id']
myDatabase = database()
cur, conn = myDatabase.sqliteConnect()
cur.execute("SELECT * FROM monitoring WHERE metasploit_upload=1")
myDatabase.sqlLiteCommit(conn)
mysqlResults = cur.fetchall()
for row in mysqlResults:
    tcp_nmap_file = str(row[11])
    udp_nmap_file = str(row[12])
    commands = """workspace """+str(metasploit_workspace)+"""
    db_import """+str(tcp_nmap_file).replace(".xml","*")+"""
    db_import """+str(udp_nmap_file).replace(".xml","*")+"""
    """
    client.call('console.write',[console_id, commands])
    res = client.call('console.read',[console_id])
    cur.execute("UPDATE monitoring SET metasploit_upload=2 WHERE id=" + str(row[0]))
    myDatabase.sqlLiteCommit(conn)
    print "import " + tcp_nmap_file
    print "import " + udp_nmap_file

#FOR NESSUS
cur, conn = myDatabase.sqliteConnect()
cur.execute("SELECT * FROM nessus WHERE scan_status='Upload in progress'")
myDatabase.sqlLiteCommit(conn)
mysqlResults = cur.fetchall()
for row in mysqlResults:
    nessus_file = str(row[9])
    commands = """workspace """+str(metasploit_workspace)+"""
    db_import """+str(nessus_file)+"""
    """
    client.call('console.write',[console_id, commands])
    res = client.call('console.read',[console_id])
    cur.execute("UPDATE nessus SET scan_status = 'Uploaded to Metasploit' WHERE id=" + str(row[0]))
    myDatabase.sqlLiteCommit(conn)
