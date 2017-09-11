from xml.dom.minidom import *
from netaddr import *
from xlsxwriter import *
import sys, sqlite3, os, re

####################################################
#Metasploit XML extract
#Use db_export -f xml /Path/XMLFile.xml
XMLfileName = "METASPLOIT XML FILE DOWNLOADED"
####################################################

class Parser():

    def __init__(self, xmldata):
        self.currentNode = None
        self.readXml(xmldata)

    def readXml(self, data):
        self.doc = parseString(data)

    def getText(self, node):
        return node.childNodes[0].nodeValue

    def getRootElement(self):
        if self.currentNode == None:
            self.currentNode = self.doc.documentElement
        return self.currentNode

    def sqlliteCreate(self, databasefile):
        try:
            os.remove(databasefile)
        except:
            ""
        conn = sqlite3.connect(databasefile)
        cur = conn.cursor()
        cur.execute('''CREATE TABLE hostsMetasploit(host_id, address, hostname, os_name, os_version, device_type, tag)''')
        cur.execute('''CREATE TABLE servicesMetasploit(service_id, host_id, port, protocol, service, status)''')
        cur.execute('''CREATE TABLE vulnsMetasploit(vuln_id, host_id, service_id, name, info, refs)''')
        conn.commit()
        return conn

    def sqliteAddRowHost(self, databaseDico, conn):
        cur = conn.cursor()
        cur.execute("INSERT INTO hostsMetasploit(host_id, address, hostname, os_name, os_version, device_type, tag) values (?, ?, ?, ?, ?, ?, ?)",(databaseDico['host_id'], databaseDico['address'], databaseDico['hostname'], databaseDico['os_name'], databaseDico['os_version'], databaseDico['device_type'],  databaseDico['tag']))
        return conn

    def sqliteAddService(self, databaseDico, conn):
        cur = conn.cursor()
        cur.execute("INSERT INTO servicesMetasploit(service_id, host_id, port, protocol, service, status) values (?, ?, ?, ?, ?, ?)",(databaseDico['service_id'],databaseDico['host_id'], databaseDico['port'], databaseDico['protocol'], databaseDico['service'], databaseDico['status']))
        return conn

    def sqliteAddVuln(self, databaseDico, conn):
        cur = conn.cursor()
        cur.execute("INSERT INTO vulnsMetasploit(vuln_id, host_id, service_id, name, info, refs) values (?, ?, ?, ?, ?, ?)",(databaseDico['vuln_id'],databaseDico['host_id'], databaseDico['service_id'], databaseDico['name'], databaseDico['info'], databaseDico['refs']))
        return conn

    def sqlLiteCommit(self, conn):
        conn.commit()
        #conn.close()

    def striphtml(self, data):
        p = re.compile(r'<.*?>')
        return p.sub('', data)

    def getHosts(self, conn):
        for ran in self.getRootElement().getElementsByTagName("host"):
            if ran.nodeType == ran.ELEMENT_NODE:
                myTab = {}
                idMeta = self.getText(ran.getElementsByTagName("id")[0])
                myTab['host_id'] = str(idMeta)
                address = self.getText(ran.getElementsByTagName("address")[0])
                myTab['address'] = str(address)
                try:
                    dns = self.getText(ran.getElementsByTagName("name")[0])
                    myTab['hostname'] = str(dns)
                except:
                    dns = ""
                    myTab['hostname'] = str(dns)
                    pass
                try:
                    os = self.getText(ran.getElementsByTagName("os-name")[0])
                    myTab['os_name'] = str(os)
                except:
                    os = ""
                    myTab['os_name'] = str(os)
                    pass
                try:
                    dType = self.getText(ran.getElementsByTagName("purpose")[0])
                    myTab['device_type'] = str(dType)
                except:
                    dns = ""
                    myTab['device_type'] = str(dType)
                    pass
                try:
                    osVersion = self.getText(ran.getElementsByTagName("os-sp")[0])
                    myTab['os_version'] = str(osVersion)
                except:
                    osVersion = ""
                    myTab['os_version'] = str(osVersion)
                    pass
                tagflag = 0
                for tags in ran.getElementsByTagName("tag"):
                    tagflag = 1
                    try:
                        tag = self.getText(tags.getElementsByTagName("name")[0])
                        myTab['tag'] = str(tag)
                    except:
                        tag = ""
                        myTab['tag'] = str(tag)
                        pass
                if tagflag == 0:
                    tag = ""
                    myTab['tag'] = str(tag)
            conn = self.sqliteAddRowHost(myTab, conn)
        self.sqlLiteCommit(conn)

    def getServices(self,conn):
        for ran in self.getRootElement().getElementsByTagName("service"):
            if ran.nodeType == ran.ELEMENT_NODE:
                myTab = {}
                srvID = self.getText(ran.getElementsByTagName("id")[0])
                myTab['service_id'] = str(srvID)
                hostID = self.getText(ran.getElementsByTagName("host-id")[0])
                myTab['host_id'] = str(hostID)
                try:
                    port = self.getText(ran.getElementsByTagName("port")[0])
                    myTab['port'] = str(port)
                except:
                    port = ""
                    myTab['port'] = str(port)
                    pass
                try:
                    proto = self.getText(ran.getElementsByTagName("proto")[0])
                    myTab['protocol'] = str(proto)
                except:
                    proto = ""
                    myTab['protocol'] = str(proto)
                    pass
                try:
                    state = self.getText(ran.getElementsByTagName("state")[0])
                    myTab['status'] = str(state)
                except:
                    state = ""
                    myTab['status'] = str(state)
                    pass
                try:
                    srvName = self.getText(ran.getElementsByTagName("name")[0])
                    myTab['service'] = str(srvName)
                except:
                    srvName = ""
                    myTab['service'] = str(srvName)
                    pass
            conn = self.sqliteAddService(myTab, conn)
        self.sqlLiteCommit(conn)

    def getVulns(self,conn):
        for ran in self.getRootElement().getElementsByTagName("vuln"):
            if ran.nodeType == ran.ELEMENT_NODE:
                myTab = {}
                vulnID = self.getText(ran.getElementsByTagName("id")[0])
                myTab['vuln_id'] = str(vulnID)
                hostID = self.getText(ran.getElementsByTagName("host-id")[0])
                myTab['host_id'] = str(hostID)
                try:
                    srvID = self.getText(ran.getElementsByTagName("service-id")[0])
                    myTab['service_id'] = str(srvID)
                except:
                    myTab['service_id'] = ""
                try:
                    srvName = self.getText(ran.getElementsByTagName("name")[0])
                    myTab['name'] = str(srvName)
                except:
                    vulnName = ""
                    myTab['name'] = str(vulnName)
                    pass
                try:
                    vulnInfo = self.getText(ran.getElementsByTagName("info")[0])
                    myTab['info'] = str(vulnInfo)
                except:
                    vulnInfo = ""
                    myTab['info'] = str(vulnInfo)
                    pass
                myrefs = "refs:"
                for refs in ran.getElementsByTagName("refs"):
                    try:
                        ref = self.getText(refs.getElementsByTagName("ref")[0])
                        myrefs = myrefs + " " + str(ref)
                    except:
                        pass
                myTab['refs'] = str(myrefs)
            conn = self.sqliteAddVuln(myTab, conn)
        self.sqlLiteCommit(conn)
        
    def createExcel(self, excelFileName, conn):
        book = Workbook(str(excelFileName))
        request1 = "SELECT DISTINCT hostsMetasploit.host_id, hostsMetasploit.address, hostsMetasploit.hostname, hostsMetasploit.os_name, hostsMetasploit.os_version, hostsMetasploit.device_type, hostsMetasploit.tag, servicesMetasploit.port, servicesMetasploit.protocol, servicesMetasploit.service, servicesMetasploit.status FROM servicesMetasploit INNER JOIN hostsMetasploit ON servicesMetasploit.host_id = hostsMetasploit.host_id"
        request2 = "SELECT DISTINCT hostsMetasploit.host_id, hostsMetasploit.address, hostsMetasploit.hostname, hostsMetasploit.os_name, hostsMetasploit.os_version, hostsMetasploit.device_type, hostsMetasploit.tag, vulnsMetasploit.vuln_id, vulnsMetasploit.name, vulnsMetasploit.info, vulnsMetasploit.refs FROM hostsMetasploit INNER JOIN vulnsMetasploit ON vulnsMetasploit.host_id = hostsMetasploit.host_id"

        cur = conn.cursor() 
        result1 = cur.execute(request1)
        cur = conn.cursor() 
        result2 = cur.execute(request2
                              )
        sheet1 = book.add_worksheet("NMAP_RESULT")
        sheet1.write(0,0, "HOST_ID")
        sheet1.write(0,1, "ID_ADDRESS")
        sheet1.write(0,2, "HOSTNAME")
        sheet1.write(0,3, "OS_NAME")
        sheet1.write(0,4, "OS_VERSION")
        sheet1.write(0,5, "DEVICE_TYPE")
        sheet1.write(0,6, "TAG")
        sheet1.write(0,7, "PORT")
        sheet1.write(0,8, "PROTOCOL")
        sheet1.write(0,9, "SERVICE_NAME")
        sheet1.write(0,10, "SERVICE_STATUS")
        line=1
        for cell in result1:
            sheet1.write(line, 0, cell[0])
            sheet1.write(line, 1, cell[1])
            sheet1.write(line, 2, cell[2])
            sheet1.write(line, 3, cell[3])
            sheet1.write(line, 4, cell[4])
            sheet1.write(line, 5, cell[5])
            sheet1.write(line, 6, cell[6])
            sheet1.write(line, 7, cell[7])
            sheet1.write(line, 8, cell[8])
            sheet1.write(line, 9, cell[9])
            sheet1.write(line, 10, cell[10])
            line += 1
            
        sheet2 = book.add_worksheet("NESSUS_RESULT")
        sheet2.write(0,0, "HOST_ID")
        sheet2.write(0,1, "ID_ADDRESS")
        sheet2.write(0,2, "HOSTNAME")
        sheet2.write(0,3, "OS_NAME")
        sheet2.write(0,4, "OS_VERSION")
        sheet2.write(0,5, "DEVICE_TYPE")
        sheet2.write(0,6, "TAG")
        sheet2.write(0,7, "VULN_ID")
        sheet2.write(0,8, "VULN_NAME")
        sheet2.write(0,9, "VULN_INFO")
        sheet2.write(0,10, "VULN_REF")
        line=1
        for cell in result2:
            sheet2.write(line, 0, cell[0])
            sheet2.write(line, 1, cell[1])
            sheet2.write(line, 2, cell[2])
            sheet2.write(line, 3, cell[3])
            sheet2.write(line, 4, cell[4])
            sheet2.write(line, 5, cell[5])
            sheet2.write(line, 6, cell[6])
            sheet2.write(line, 7, cell[7])
            sheet2.write(line, 8, cell[8])
            sheet2.write(line, 9, cell[9])
            sheet2.write(line, 10, cell[10])
            line += 1
        try:
            os.remove(excelFileName)
        except:
            ""
        book.close()

myfile = open(XMLfileName,"r")
data = myfile.read()
print "file opened..."
myParser = Parser(data)
conn = myParser.sqlliteCreate(XMLfileName.replace(".xml",".db"))
print "database creation..."
myParser.getHosts(conn)
print "hosts imported..."
myParser.getServices(conn)
print "services imported..."
myParser.getVulns(conn)
print "vulnerabilities imported..."
myParser.createExcel(XMLfileName.replace(".xml",".xlsx"), conn)
print "excel file created..."
