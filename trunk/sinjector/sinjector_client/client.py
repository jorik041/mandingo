#!/usr/bin/env python

import xmlrpclib
import sys
import time
import select
import subprocess
import re
import base64
import hashlib
import os
import signal
import threading

VMACHINE="windows xp sp3"
SNAPSHOT="vbox_ao"
HOSTONLY_IFACE="vboxnet0"

TCPDUMP="/usr/sbin/tcpdump"
SINJECTOR="C:\\sInjector\\sinjector.exe"

SECONDS=60
MAXSTARTWAIT=30

SUSPENDVM=True
s=None
pr=subprocess.Popen('ps aux|grep client.py', shell=True, stdout=subprocess.PIPE).stdout.read()
for line in pr.split("\n"):
	res=re.findall("^[^\s]+\s+(\d+)",line)
	if len(res):
		if res[0]!=str(os.getpid()):
			try:
				#print "killing process "+res[0]
				subprocess.Popen('kill -9 %s 2>/dev/null' % res[0],shell=True)
			except:
				pass

if len(sys.argv)<3:
	print "Usage: %s <host> <sample> (ex: 192.168.56.101 fileToAnalyze.ex_)" % sys.argv[0]
	sys.exit()

PORT=8000
HOST=sys.argv[1] #192.168.56.101
SAMPLE=sys.argv[2]

errorCount=0

#kill other client processes

#print ">> Type \"quit\" and press [intro] to finalize..."

def heardEnter():
	i,o,e = select.select([sys.stdin],[],[],0.0001)
	for s in i:
		if s == sys.stdin:
			input = sys.stdin.readline()
			return input.rstrip()
	return ""

def suspendVM():
	print "[vm] suspending..."
	subprocess.Popen('VBoxManage controlvm "%s" poweroff' % VMACHINE, shell=True)
	time.sleep(3)

def screenshotVM(md5,name):
	print "[vm] taking screenshot: screenshot-%s.png" % name
	subprocess.Popen('VBoxManage controlvm "%s" screenshotpng results/%s/screenshot-%s.png' % (VMACHINE,md5,name), shell=True)
	time.sleep(3)

def startVM():
	print "[vm] restoring snapshot: %s" % SNAPSHOT
	subprocess.Popen('VBoxManage snapshot "%s" restore "%s"' % (VMACHINE,SNAPSHOT), shell=True)
	while 1:
		res=subprocess.Popen('VBoxManage showvminfo "%s"' % VMACHINE,shell=True, stdout=subprocess.PIPE).stdout.read()
		r=re.findall("State:\s+([^\s]+)",res)
		if not len(r):
			time.sleep(2)
		else:
			print "\033[1m[vm] Status: %s\033[0m" % r[0]
			if r[0]!="saved" and r[0]!="restoring":
				print "[error] VM is running? Trying to suspend and restart..."
				suspendVM()
				return startVM()
			if r[0]=="saved":
				break
			else:
				time.sleep(2) #saving?		
	print "[vm] starting vm"
	options="--type=headless"
	options=""
	subprocess.Popen('VBoxManage startvm "%s" %s' % (VMACHINE,options), shell=True)

if SUSPENDVM:
	startVM()
	time.sleep(5)

import socket

def DoesServiceExist(host, port):
	host_addr = ""
	global errorCount
	try:
		host_addr = socket.gethostbyname(host)

		s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
		s.settimeout(1)
		s.connect((host, port))
		s.close()
		errorCount=0
	except Exception,e:
		print "Error(%d) - %s" % (errorCount,str(e))
		errorCount+=1
		return False

	return True

print "[info] Trying to connect to remote port (%d seconds max)..." % MAXSTARTWAIT
port_open=False
for i in range(MAXSTARTWAIT):
	if DoesServiceExist(HOST,PORT):
		port_open=True
		#print "[info] Port open"
		break
	time.sleep(1)

if not port_open:
	print "[timeout] communications port %d is not open at %s after %d seconds... :p" % (PORT,HOST,MAXSTARTWAIT)
	sys.exit(0)
print "[info] Remote port is open :)"

s = xmlrpclib.ServerProxy('http://%s:%d' % (HOST,PORT), allow_none=True)

def Upload(filename):
	global md5
	data=""
	with open(filename,"r") as _input:
		bytes=_input.read()
		md5=hashlib.md5(bytes).hexdigest()
		data=xmlrpclib.Binary(bytes)
	_input.close()
	print "Uploading %s (md5: %s)" % ("sample",md5) #do not show filename
	return s.Upload(data)

res=Upload(SAMPLE) #"samples/ZeuS_binary_14a18b30c40f5a4fafe08e0c21cc5844.ex_")

#make results dir
try:
	os.mkdir("results/%s" % md5)
except Exception,e:
	print str(e)

r=re.findall("to: (.*)",res)
filename=r[0]
print "Sample uploaded: %s" % filename

tcpdump_pid=None

class Tcpdump(threading.Thread):
	def __init__(self):
		threading.Thread.__init__(self)
	def run(self):
		global md5
		cmd=[TCPDUMP,"-i",HOSTONLY_IFACE,"-n","-s","0","-w","results/%s/network.pcap" % md5,"!port","8000"]
		cmdline=" ".join(cmd)
		print "Launching:",cmdline
		self.process=subprocess.Popen(cmd,shell=False)
	def close(self):
		subprocess.Popen('kill %d 2>/dev/null' % self.process.pid,shell=True)
tcpdump = Tcpdump()
tcpdump.start()

def purge(dir, pattern):
    for f in os.listdir(dir):
    	if re.search(pattern, f):
    		os.remove(os.path.join(dir, f))

#remove old results
purge("results/%s/" % md5,".*")

print "Saving processes list..."
processes=s.Run("\"%s\" /l" % SINJECTOR)
with open("results/%s/processes.txt" % md5,"w") as _output:
	_output.write(processes)
_output.close()

print "Removing old logs..."
s.Run("del c:\\newlog.text")

print "Running sinjector..."
s.Run("cmd /c \"start %s /x %s & pause\"" % (SINJECTOR,filename),True)

def ReadFileRemote(filename):
	global s
	#print "Requesting %s" % filename
	if DoesServiceExist(HOST,PORT):
		try:
			data=base64.b64decode(s.ReadFile(filename))
			return data
		except Exception:
			#try to reconnect
			s = xmlrpclib.ServerProxy('http://%s:%d' % (HOST,PORT), allow_none=True)
			pass
	return None

loglines=0
def LastLog(log):
	global loglines
	count=0
	res=""
	for line in log.split("\r\n"):
		if count>=loglines and len(line):
			res+=line+"\n"
		count+=1
	loglines=count
	if len(res):
		res=res[:len(res)-1]
	return res

print "Main loop starts..."
for i in range(SECONDS):
	if i==1 or i==int(SECONDS/1.5) or i==SECONDS-1:
		screenshotVM(md5,"%dsecs" % i)
	try:
		log=ReadFileRemote("c:\\newlog.text")
		if log:
			lastlog=LastLog(log)
			if len(lastlog):
				print lastlog
			if len(log):
				with open("results/%s/newlog.text" % md5,"w") as _output:
					_output.write(log)
				_output.close()
	except Exception,e:
		print ">> Error: %s" % str(e)
	time.sleep(1)
	input=heardEnter()
	if len(input):
		print ">> ["+input+"]"
		if input=="quit":
			break

def createdFiles(filename):
	log=""
	with open(filename,"r") as f:
		log=f.read()
	f.close()
	files=set()
	for line in log.split("\r\n"):
		if len(re.findall("\[CreateFile.*gWRI",line)):
			res=re.findall(".*\]\s+(.*)",line)
			if res[0][:2]=="\\\\":
				continue
			files.add(res[0])
	return files

def deletedFiles(filename):
	log=""
	with open(filename,"r") as f:
		log=f.read()
	f.close()
	files=set()
	for line in log.split("\r\n"):
		if len(re.findall("\[DeleteFile",line)):
			res=re.findall(".*\]\s+(.*)",line)
			if res[0][:2]=="\\\\":
				continue
			files.add(res[0])
	return files

#save the deleted created
files=deletedFiles("results/%s/newlog.text" % md5)
for fname in files:
	sortname=fname[1+fname.rfind("\\"):]
	outname="results/%s/deleted_%s" % (md5,fname)
	print ">> Saving [DeleteFile]: %s" % outname
	data=ReadFileRemote("c:\\temp\\%s" % sortname)
	if data:
		if len(data):
			with open(outname,"w") as _out:
				_out.write(data)
			_out.close()
		else:
			print "[info] Empty file, skipped"

#save the files created
files=createdFiles("results/%s/newlog.text" % md5)
for fname in files:
	outname="results/%s/%s" % (md5,os.path.basename(fname))
	print ">> Saving [CreateFile]: %s " % outname
	if not os.path.isdir(outname):
		data=ReadFileRemote(fname)
		if data:
			if len(data):
				with open(outname,"w") as _out:
					_out.write(data)
				_out.close()
			else:
				print "[info] Empty file, skipped"
		else:
			print "[error] file is a directory..."

#save the final processess list
print "Saving final processes list..."
try:
	processes=s.Run("\"%s\" /l" % SINJECTOR)
	with open("results/%s/processes_final.txt" % md5,"w") as _output:
		_output.write(processes)
	_output.close()
except Exception:
	pass

if SUSPENDVM:
	#suspend VM and terminate
	suspendVM()

tcpdump.close()

print "Results stored in: results/%s" % md5
sys.exit(0)
