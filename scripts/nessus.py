#!/usr/bin/env python2.7
from __future__ import division
import sqlite3, requests, json
import sys, os, re, time, socket
from netaddr import *

############################################################################
# SETTINGS
############################################################################
access_key = 'NESSUS ACCESS KEY'
secret_key = 'NESSUS SECRET KEY'
net_policy_template_id="Nessus Network Policy Template"
net_policy_id="Nessus Network Policy ID"
web_policy_template_id="Nessus Web Policy Template"
web_policy_id="Nessus Web Policy ID"
folder_id="Nessus Folder ID"
url = 'Nessus URL'
databaseFileLocation = "NESSUS DATABASE PATH LOCATION"
output_directory = "NESSUS OUTPUT DIRECTORY"
############################################################################
headers = {'Content-type': 'application/json', 'X-ApiKeys': 'accessKey='+access_key+'; secretKey='+secret_key}

class database():
    def sqliteConnect(self):
        databasefile = databaseFileLocation
        conn = sqlite3.connect(databasefile)
        cur = conn.cursor()
        return cur, conn

    def sqlLiteCommit(self, conn):
        conn.commit()
        #conn.close()

#Discovery PGM - Beginning
myDatabase = database()
cur, conn = myDatabase.sqliteConnect()
cur.execute("SELECT * FROM nessus")
myDatabase.sqlLiteCommit(conn)
mysqlResults = cur.fetchall()
for row in mysqlResults:
    scan_status=str(row[6])
    scan_name=str(row[2])
    if scan_status ==  "Not Started":
        IPsList=str(row[8])
        #Policy dispatch
        policy=str(row[3])
        if policy == "Basic":
            policy_template = net_policy_template_id
            policy_id = net_policy_id
        elif policy == "Web":
            policy_template = web_policy_template_id
            policy_id = web_policy_id
        data = {
        "uuid": policy_template,
        "settings": {
            "name": "RScan_"+scan_name+"_"+policy,
            "description": "RScan based on "+policy,
            "enabled": "false",
            "launch": "ON_DEMAND",
            "folder_id": folder_id,
            "policy_id": policy_id,
            "text_targets": IPsList,
        }
        }
        requests.packages.urllib3.disable_warnings()
        req = requests.request('POST', url+'/scans', headers=headers, data=json.dumps(data), verify=False)
        data = req.json()
        for key, value in data.items():
            for item in value.items():
                if str(item[0]) == "id":
                    sid=str(item[1])

        #Database UPDATE
        cur.execute("UPDATE nessus SET scan_id = "+ str(sid) + ", scan_status='Scan created' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)

    elif scan_status ==  "Launched":
        scan_id=str(row[1])
        #Launch scan
        requests.packages.urllib3.disable_warnings()
        req = requests.request('POST', url+'/scans/'+scan_id+'/launch', headers=headers, verify=False)
        data = req.json()
        print data
        #Database UPDATE
        cur.execute("UPDATE nessus SET scan_status='Running' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)
    
    elif scan_status ==  "Generating Report" or scan_status == "Download in progress":
        scan_id=str(row[1])
        data = { "scan_id": scan_id, "format": "nessus"}
        requests.packages.urllib3.disable_warnings()
        req = requests.request('POST', url+'/scans/'+scan_id+'/export', headers=headers, data=json.dumps(data), verify=False)
        data = req.json()
        for value in data.items():
            if value[0] == "file":
                download_fileid = str(value[1])
                print download_fileid
        #SQL Requests
        cur.execute("UPDATE  nessus SET report_id='"+ str(download_fileid) +"', scan_status='Download in progress' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)
        report_status = "nok"
        while report_status != "ready":
            file_id = download_fileid
            data = { "scan_id": scan_id, "format": "nessus"}
            requests.packages.urllib3.disable_warnings()
            req = requests.request('GET', url+'/scans/'+scan_id+'/export/'+file_id+'/status', headers=headers, data=json.dumps(data), verify=False)
            data = req.json()
            print data
            for key, value in data.items():
                report_status = value
                print value
                time.sleep(1)
            
        req = requests.request('GET', url+'/scans/'+scan_id+'/export/'+file_id+'/download', headers=headers, data=json.dumps(data), verify=False)
        print req
        report = req.content
        filename = output_directory +"/"+scan_name+".xml" 
        output = open(filename,'w')
        output.write(report)
        output.close()
        #SQL Requests
        cur.execute("UPDATE  nessus SET report_file='"+ str(filename) +"', scan_status='Report downloaded' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)
    elif scan_status == "Running" or scan_status == "cancelled" or scan_status == "paused":
        scan_status ==  str(row[6])
        scan_id=str(row[1])
        requests.packages.urllib3.disable_warnings()
        req = requests.request('GET', url+'/scans/'+scan_id, headers=headers, verify=False)
        data = req.json()
        for key, value in data.items():
            try:
                for item in value.items():
                    if item[0] == "status":
                        scanStatus = item[1]
                        print str(scanStatus)
            except:
                ""
        #SQL Requests
        cur.execute("UPDATE nessus SET scan_status='"+ str(scanStatus) +"' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)
