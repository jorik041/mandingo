#!/usr/bin/env python
import sys
import re

if len(sys.argv)<2:
	print "Usage: %s <sinjector_logfile>" % sys.argv[0]
	sys.exit()

filename=sys.argv[1]
log=""
with open(filename) as f:
	log=f.read()

libs={}
for line in log.split("\n"):
	m=re.findall("\[(LoadLibrary|GetModuleHandle).*?\] handle=(\d+) (\"[^\"]+\")",line)
	if len(m) and m[0][2].upper()!="\"(NULL)\"":
		libs[m[0][1]]=m[0][2].upper()
count=0
for line in log.split("\n"):
	m=re.findall("\[GetProcAddress] handle=(\d+)",line)
	if len(m):
		if m[0] not in libs:
			count+=1
			libs[m[0]]="\"unknown%.2d\"" % count
import operator
s = sorted(libs.items(), key=operator.itemgetter(1))
for lib in s:
	print "handle=%s %s" % (lib[0],lib[1])
