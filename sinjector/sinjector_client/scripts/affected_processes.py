#!/usr/bin/env python
import re
import sys
import os

main_pid=None

def affectedProcesses(filename):
	global main_pid
	log=""
	c_p={}
	o_p={}
	with open(filename,"r") as f:
		log=f.read()
	f.close()
	affected_processes=set()
	for line in log.split("\r\n"):
		if len(re.findall("\[injector\] EXECUTING",line)):
			res=re.findall('"([^\"]+)" HOOKING PID (\d+)',line)
			if line.find("C:\sinjector")!=-1:#get the main pid
				pres=re.findall("\[([^\s*\]]+)",line)
				if pres:
					main_pid=pres[0].rstrip()
			#print res[0][0]+" - "+res[0][1]
			affected_processes.add((res[0][1].rstrip(),"created"))
			c_p[res[0][1]]=res[0][0]
		if len(re.findall("\[OpenProcess",line)):
			res=re.findall("PID=(\d+).+?\) (.+)",line)
			if len(res):
				o_p[res[0][0]]=res[0][1]
				affected_processes.add((res[0][0],"open"))
		if len(re.findall("\[CreateProcess",line)):
			res=re.findall("PID=(\d+)",line)
			#TODO: review this
			if not len(res):
				continue
			affected_processes.add((res[0].rstrip(),"created"))
			app=re.findall("Thread=\d+ (.+)",line)
			if len(app):
				app[0]=app[0].replace("SUSPENDED ","")
				c_p[res[0].rstrip()]=app[0]
			#app=re.findall('\s(\".+)\"',line)
			#TODO: review this
			#if len(app):
			#	c_p[res[0].rstrip()]=app[0]
		if len(re.findall("\[WriteProcessMemory",line)):
			res=re.findall("PID=(\d+)",line)
			affected_processes.add((res[0].rstrip(),"written"))
		if len(re.findall("\[CreateRemoteThread",line)):
			res=re.findall("PID=(\d+)",line)
			affected_processes.add((res[0].rstrip(),"rthread"))
	return dict(affected=affected_processes,created=c_p,other=o_p)

if len(sys.argv)<2:
	print "Usage: %s <sinjector_logfile>" % sys.argv[0]
	sys.exit()

def runningProcesses(filename):
	p={}
	#print "Reading: %s" % filename
	try:
		with open(filename,"r") as f:
			lines=f.read()
		f.close()
	except Exception,e:
		print "[error] %s" % e
		return p
	for line in lines.split("\n"):
		res=re.findall("\[([^\s*\]]+)\s*\]\s*([^#]+)",line)
		if len(res):
			p[res[0][0]]=res[0][1]
	return p

def hookedProcesses(filename):
	global main_pid
	p=set()
	try:
		with open(filename,"r") as f:
			lines=f.read()
		f.close()
	except Exception,e:
		print "[error] %s" % e
		return p
	for line in lines.split("\n"):
		res=re.findall("^\[([^\s*\]]+)\s*\]",line)
		if len(res):
			if res[0]!=main_pid: #do not trace as hooked the main pid (launcher)
				p.add((res[0],"pre_hooked"))
	return p

old_processes=runningProcesses(os.path.dirname(sys.argv[1])+"/processes.txt")
final_processes=runningProcesses(os.path.dirname(sys.argv[1])+"/processes_final.txt")
files=affectedProcesses(sys.argv[1])
hooked=hookedProcesses(sys.argv[1])

#add hooked processes missed in affected processes
a=set()
for hfile in hooked:
	found=False
	for file in files["affected"]:
		if file[0]==hfile[0]:
			found=True
	if not found:
		files["affected"].add(hfile)

new_processes={}
for fp in final_processes.keys():
	if not fp in old_processes:
		new_processes[fp]=final_processes[fp].rstrip()

#add newprocesses (not found at start) in affected processes
diff_processes={}
a=set()
for dfile in new_processes:
	if new_processes[dfile]=="sinjector.exe" or new_processes[dfile]=="cmd.exe": #do not log this final processes
		continue
	if dfile==main_pid:
		continue
	found=False
	for file in files["affected"]:
		if file[0]==dfile[0]:
			found=True
	if not found:
		files["affected"].add((dfile,"alive"))

for file in files["affected"]:
	pid=file[0]
	action=file[1]
	app="unknown"
	msg="unknown"
	if pid in files["other"]:
		app=files["other"][pid]
		msg="other"
	if pid in old_processes:
		app=old_processes[pid]
		msg="running"
	if pid in new_processes:
		app=new_processes[pid]
		msg="running"
	if pid in files["created"]:
		app=files["created"][pid]
		msg="new"
	print "%-6s %-8s %7s %s" % (pid,action,msg,app)
