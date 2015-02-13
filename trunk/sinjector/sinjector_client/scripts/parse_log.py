#!/usr/bin/env python
import re
import sys

pid=""

if len(sys.argv)<2:
	print "Usage: %s <sinjector_logfile> [pid]"
	sys.exit()

logfile=sys.argv[1]
noreg=False
nogetproc=False
if len(sys.argv)>2:
	for i in range(2,len(sys.argv)):
		if sys.argv[i]=="-reg":
			noreg=True
		if sys.argv[i]=="-proc":
			nogetproc=True
		if sys.argv[i]=="-pid":
			pid=sys.argv[i+1]

with open(logfile,"r") as f:
	log=f.read()
f.close()

uniq=[]

for line in log.split("\r\n"):
	if len(pid) and not len(re.findall("^\[%s" % pid,line)):
		continue
	if noreg and len(re.findall("RegQueryValueEx|RegOpenKey",line)):
		continue
	if nogetproc and len(re.findall("GetProcAddress|LoadLibrary|GetModuleHandle",line)):
		continue
	if line not in uniq:
		uniq.append(line)

color={"blue":"\033[36;1m","orange":"\033[33m","no":"\033[0m","red":"\033[31m","inv":"\033[7m","gray":"\033[2m","pink":"\033[35m","green":"\033[32m"}

for u in uniq:
	c_s=""
	c_e=""
	if len(re.findall("\[(InternetConnect.|inet_addr|connect|HttpOpenRequest.)\]",u)):
		c_s=color["pink"]
		c_e=color["no"]
	if len(re.findall("\[(RegSetValueEx.|RegCreateKey(A|W|ExA|ExW))\]",u)):
		c_s=color["green"]
		c_e=color["no"]
	if len(re.findall("\[(CreateProcess|ShellExecute|CreateRemoteThread)",u)):
		c_s=color["blue"]
		c_e=color["no"]
	if len(re.findall("RegQueryValueEx",u)) and len(re.findall("UNKNOWN",u)):
		c_s=color["gray"]
		c_e=color["no"]
	if len(re.findall("RegOpenKey",u)) and len(re.findall("handle=0x0 ",u)):
		c_s=color["gray"]
		c_e=color["no"]
	if len(re.findall("\[GetModuleHandle",u)) and len(re.findall("handle=0 ",u)):
		c_s=color["gray"]
		c_e=color["no"]
	if len(re.findall("\[CopyFile",u)):
		c_s=color["orange"]
		c_e=color["no"]
	if len(re.findall("gWRI",u)):
		c_s=color["orange"]
		c_e=color["no"]
	if len(re.findall("\[DeleteFile",u)):
		c_s=color["red"]
		c_e=color["no"]
	if len(re.findall("\[REINJECT|injector\]",u)):
		c_s=color["inv"]
		c_e=color["no"]
	print "%s%s%s" % (c_s,u,c_e)
