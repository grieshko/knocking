#!/usr/bin/env python2.7
from __future__ import division
import sqlite3, requests, json
import sys, os, re, time, socket, nmap
from netaddr import *
from multiprocessing import Process

############################################################################
# SETTINGS
############################################################################
databaseFileLocation = "NMAP DATABASE LOCATION"
nmap_output_file_path = "NMAP OUTPUT FILE"
patternRange = '[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+\/[0-9]+'
patternIP = '[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+'
progRange = re.compile(patternRange)
progIP = re.compile(patternIP)

#NMAP PortScan ForkCommand
#Portscan
HostsUPthreshold = "<NUMBER OF HOSTS SCANNED BY THREAD>"
nbThreads = "<NUMBER OF // THREADS>"
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

class NMapProcess():
    def nmapScan(self, IPranges, nmap_values, nmap_file, numThread):
        nm = nmap.PortScanner()
        nm.scan(hosts=IPranges, arguments=nmap_values)
        for host in nm.all_hosts():
            hostname = nm[host].hostname()
            for proto in nm[host].all_protocols():
                lport = nm[host][proto].keys()
                lport.sort()
                for port in lport:
                    #host;protocol;port;name;state;product;extrainfo;reason;version;conf
                    status = nm[host][proto][port]['state']
                    service = nm[host][proto][port]['name']
                    os = nm[host][proto][port]['version']
                    other = str(nm[host][proto][port]['product']) + " " + str(nm[host][proto][port]['extrainfo'])
                    request = "INSERT into nmap (zone, ip, dns, os, protocol, port, service, status, other) values ('"+str(zone)+"','"+str(host)+"','"+str(hostname)+"','"+str(os)+"','"+str(proto)+"','"+str(port)+"','"+str(service)+"','"+str(status)+"','"+str(other)+"')"
                    cur.execute(request)
                    myDatabase.sqlLiteCommit(conn)

        #Save NMAP OUTPUT
        nmap_output_file_thread = nmap_file + "_" + str(numThread)+".xml"
        xml_file = nm.get_nmap_last_output()
        f = open(nmap_output_file_thread, 'w')
        print nmap_output_file_thread + " created..."
        f.write(xml_file)
        f.close()

    def createProcess(self, IPRanges, nmap_values, nmap_files, numThread):
        p = Process(target=myProcess.nmapScan, args=(IPRanges, nmap_values, nmap_files, numThread,))
        p.start()
        print "Process launched... (pid-" + str(p.pid) + ")"
        #p.join()
        return p

    def checkforProcesses(self, pList):
        NumberofActiveProcesses = 0
        for p in pList:
            if p.is_alive():
                NumberofActiveProcesses +=1
        return NumberofActiveProcesses

    def launchProcess(self, IPRanges, nmap_values, nmap_files, numThread, processesList, nbThreads):
        while True:
            NumberofActiveProcesses = self.checkforProcesses(processesList)
            if NumberofActiveProcesses <= nbThreads:
                p = self.createProcess(IPRanges, nmap_values, nmap_files, numThread)
                processesList.append(p)
                return processesList
            else:
                time.sleep(10)

#Get all values
myDatabase = database()
myProcess = NMapProcess()
processList =[]
dnm = nmap.PortScanner()
cur, conn = myDatabase.sqliteConnect()
cur.execute("SELECT * FROM monitoring")
myDatabase.sqlLiteCommit(conn)
mysqlResults = cur.fetchall()
for row in mysqlResults:
    scan_status = row[10]
    dScan = str(row[7])
    tcpScan = str(row[8])
    udpScan = str(row[9])
    if scan_status == 1 and dScan == "Launched":
        #Launch discovery scan
        IPranges=str(row[2])
        NMaparguments="-n -sn"
        dnm.scan(hosts=IPranges, arguments=NMaparguments)
        hosts_list = [(x, dnm[x]['status']['state']) for x in dnm.all_hosts()]
        flag = 0
        hostsUP = ""
        nbUP = 0
        for host, status in hosts_list:
            print host
            if status == "up" and flag == 0:
                hostsUP = str(host)
                flag = 1
                nbUP = 1
            elif status == "up" and flag ==1:
                hostsUP = hostsUP + " " + str(host)
                nbUP +=1

        #Number of Host/Range Calculation
        rangeList = str(row[2]).split("\n")
        nb = 0
        for ips in rangeList:
            result = progRange.match(str(ips))
            result2 = progIP.match(str(ips))
            if result:
                value = str(result.group(0))
                ipRange = IPNetwork(str(value))
                nb = ipRange.size + nb
            elif result2 :
                nb += 1
        nb3 = (nbUP*100)/nb
        nbf1 = str(nb3).split('.')[0]
        nbf2 = str(nb3).split('.')[1][:2]
        nbf = nbf1 + '.' + nbf2
        pHost = nb
        percentage = nbf
        #SQL Requests
        cur.execute("UPDATE monitoring SET hosts_up='"+str(nbUP)+"', potential_hosts='"+str(pHost)+"', hosts_up_percentage='"+str(percentage)+"%', hosts_up_list='"+ str(hostsUP) +"', dScan='Completed' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)

    elif scan_status == 2 and tcpScan == "Launched":
        #Launch portscan
        zone = str(row[1])
        HostsUP=str(row[5])
        nbHostsUP=row[4]
        nmap_output_file = nmap_output_file_path + "/TCP_" + zone + ".xml"
        nmap_values = "-Pn -O -g88 --randomize-hosts -p T:21,22,23,25,49,53,65,79,80,81,88,110,111,123,135,137,138,139,143,150,161,170,179,194,201,389,443,445,513,514,515,520,587,902,1080,1194,1352,1414,1433,1521,1629,1883,2049,3128,3306,3389,3485,5060,5222,5357,5432,5632,5666,5900,5901,5902,5903,5904,5905,5938,6000,6001,6002,6003,6004,6005,6129,6667,8080,8443,8880,9001,9443,9090,9100,27017"
        #FORK
        var = 0
        HostsUPList = ""
        if nbHostsUP <= HostsUPthreshold:
            processList = myProcess.launchProcess(HostsUP, nmap_values, nmap_output_file, '1', processList, nbThreads)
            print str(zone) + "- launchProcess: " + str (var)+ " / HostsUP: " + str(nbHostsUP)
            var +=1
        else:
            part = 0
            nbHostsUPToGroup = 0
            for ip in HostsUP.split(" "):
                HostsUPList = HostsUPList + " " + str(ip)
                nbHostsUPToGroup += 1
                if nbHostsUPToGroup == HostsUPthreshold:
                    ipList = HostsUPList
                    part += 1
                    print str(ipList) + "-" + str(part) + "-" + str(nbThreads)  
                    processList = myProcess.launchProcess(ipList, nmap_values, nmap_output_file, str(part), processList, nbThreads)
                    print  str(zone) + "- launchProcess: " + str (var)+ " / HostsUP: " + str(nbHostsUP) + " / Part: " + str(part)
                    var += 1
                    nbHostsUPToGroup = 0
                    HostsUPList = ""
            if  nbHostsUPToGroup > 0:
                ipList = HostsUPList
                part += 1
                processList = myProcess.launchProcess(ipList, nmap_values, nmap_output_file, str(part), processList, nbThreads)
                print str(zone) + "- launchProcess: " + str (var)+ " / HostsUP: " + str(nbHostsUP) + " / Part: " + str(part) + " -> last part"
        #Update DB with NMAP Files
        cur.execute("UPDATE monitoring SET tcpScan='Completed', tcpfile_path='"+nmap_output_file+"' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)

    elif scan_status == 3 and udpScan == "Launched":
        #Launch portscan
        zone = str(row[1])
        HostsUP=str(row[5])
        nbHostsUP=row[4]
        nmap_output_file = nmap_output_file_path + "/UDP_" + zone + ".xml"
        nmap_values = "-sU -n -Pn --randomize-hosts -p T:53,68,69,123,161,514,546,1194,6000"
        #FORK
        var = 0
        HostsUPList = ""
        if nbHostsUP <= HostsUPthreshold:
            processList = myProcess.launchProcess(HostsUP, nmap_values, nmap_output_file, '1', processList, nbThreads)
            print str(zone) + "- launchProcess: " + str (var)+ " / HostsUP: " + str(nbHostsUP)
            var +=1
        else:
            part = 0
            nbHostsUPToGroup = 0
            for ip in HostsUP.split(" "):
                HostsUPList = HostsUPList + " " + str(ip)
                nbHostsUPToGroup += 1
                if nbHostsUPToGroup == HostsUPthreshold:
                    ipList = HostsUPList
                    part += 1
                    processList = myProcess.launchProcess(ipList, nmap_values, nmap_output_file, str(part), processList, nbThreads)
                    print  str(zone) + "- launchProcess: " + str (var)+ " / HostsUP: " + str(nbHostsUP) + " / Part: " + str(part)
                    var += 1
                    nbHostsUPToGroup = 0
                    HostsUPList = ""
            if  nbHostsUPToGroup > 0:
                ipList = HostsUPList
                part += 1
                processList = myProcess.launchProcess(ipList, nmap_values, nmap_output_file, str(part), processList, nbThreads)
                print str(zone) + "- launchProcess: " + str (var)+ " / HostsUP: " + str(nbHostsUP) + " / Part: " + str(part) + " -> last part"
        #Update DB with NMAP Files
        cur.execute("UPDATE monitoring SET udpScan='Completed', udpfile_path='"+nmap_output_file+"' WHERE id=" + str(row[0]))
        myDatabase.sqlLiteCommit(conn)
