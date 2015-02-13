#!/usr/bin/env python
import re
import sys

def createdFiles(filename):
	log=""
	with open(filename,"r") as f:
		log=f.read()
	f.close()
	files=set()
	pids={}
	for line in log.split("\r\n"):
		if len(re.findall("\[CreateFile.*gWRI",line)):
			res=re.findall("\[(.*?)\]\s+[^\]]+\]\s+(.*)",line)
			if len(res): #sinjector bug, no name of created file?
				files.add(res[0][1])
				if res[0][1] in pids:
					pids[res[0][1]].append(res[0][0].rstrip())
				else:
					pids[res[0][1]]=list()
					pids[res[0][1]].append(res[0][0].rstrip())
		#[19936 ] [CopyFileA] "C:\sinjector\binary4" "C:\WINDOWS\system32\ShellExt\afQGF.EXE"
		if len(re.findall("\[CopyFile",line)):
			res=re.findall("\[(.*?)\]\s+[^\]]+\]\s+\"[^\"]+\"\s\"(.+)\"",line)
			files.add(res[0][1])
			if res[0][1] in pids:
				pids[res[0][1]].append(res[0][0].rstrip())
			else:
				pids[res[0][1]]=list()
				pids[res[0][1]].append(res[0][0].rstrip())			
	l=list(files)
	l.sort()
	return dict(files=l,pids=pids)

if len(sys.argv)<2:
	print "Usage: %s <sinjector_logfile>" % sys.argv[0]
	sys.exit()

files=createdFiles(sys.argv[1])
#print files["pids"]
#sys.exit()
for file in files["files"]:
	print "%s|%s" % (file,",".join(set(files["pids"][file])))

