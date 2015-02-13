#!/usr/bin/env python

import sys,re

if len(sys.argv)==1:
	print "Usage: %s <sinjector_logfile> [pid] [handle]" % sys.argv[0]
	sys.exit()

pid=None
if len(sys.argv)>2:
	pid=int(sys.argv[2])

handle=None
if len(sys.argv)>3:
	handle=sys.argv[3]

data = open(sys.argv[1],'rb').read()

def findRegQuery(pid,handle):
	global data
	querys=[]
	index=0
	q=set()
	for line in data.split("\n"):
		line=line.strip()
		index+=1
		m=re.findall("^\[([^\]]+)\] \[(RegQueryValue.*)\].+handle\(([^\)]+)\)",line)
		if m:
			if pid and pid!=int(m[0][0]):
				continue
			if handle and handle!=m[0][2]:
				continue
			if line not in querys and not line in q:
				q.add(line)
				querys.append({"line":line,"index":index})
	return querys

def findRegOpenBefore(pid,handle,index):
	global data
	found=False
	lines=data.split("\n")
	for i in range(index,0,-1):
		line=lines[i].strip()
		m=re.findall("^\[([^\]]+)\] \[(RegOpenKeyEx[AW]|RegOpenKey[AW]|RegCreateKey.*)\] handle=([^\s]+)",line)
		if not m:
			continue
		if pid and pid!=int(m[0][0]):
			continue
		if handle and handle!=m[0][2]:
			continue
		newhandle=""
		m=re.findall("handle\(([^\)]+)\)",line)
		if len(m):
			newhandle=m[0]
		return {"line":line,"index":i,"handle":newhandle}

res=findRegQuery(pid,handle)
if not handle:
	for r in res:
		print r["line"]

if handle:
	for r in res:
		op=findRegOpenBefore(pid,handle,r["index"])
		if op:
			if re.findall("handle\(([^\)]+)\)",op["line"]):
				op2=findRegOpenBefore(pid,op["handle"],op["index"]-1)
				if op2:
					if re.findall("handle\(([^\)]+)\)",op2["line"]):
						op3=findRegOpenBefore(pid,op2["handle"],op2["index"]-1)
						if op3:
							print op3["line"]
					print op2["line"]
			print op["line"]
		print r["line"]
		print 

