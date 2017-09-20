#!/usr/bin/env python2.7
from __future__ import division
import sys, os, re, time, sqlite3
from time import gmtime, strftime
from netaddr import *
from xlsxwriter import *

##############################################
databasefile = "DATABASE PATH LOCATION" 
excelFileName = "XLS OUTPUT PATH"
##############################################
book = Workbook(str(excelFileName))

#XLSX FILE
sheet1 = book.add_worksheet("NMAP_PORTSCAN_RESULT")
sheet1.write(0,0, "ZONE")
sheet1.write(0,1, "ID_ADDRESS")
sheet1.write(0,2, "HOSTNAME")
sheet1.write(0,3, "OS")
sheet1.write(0,4, "PROTOCOL")
sheet1.write(0,5, "PORT")
sheet1.write(0,6, "SERVICE_NAME")
sheet1.write(0,7, "PORT_STATUS")
sheet1.write(0,9, "COMMENT")

#DATABASE CONNECTION
conn = sqlite3.connect(databasefile)
cur = conn.cursor()
cur.execute("SELECT * FROM nmap")
conn.commit()
mysqlResults = cur.fetchall()
exline = 1
for row in mysqlResults:
    #Create XLSX FINAL
    sheet1.write(exline, 0, row[1])
    sheet1.write(exline, 1, row[2])
    sheet1.write(exline, 2, row[3])
    sheet1.write(exline, 3, row[4])
    sheet1.write(exline, 4, row[5])
    sheet1.write(exline, 5, row[6])
    sheet1.write(exline, 6, row[7])
    sheet1.write(exline, 7, row[8])
    sheet1.write(exline, 8, row[9])
    exline +=1
book.close()
print "Excel file generated..."
