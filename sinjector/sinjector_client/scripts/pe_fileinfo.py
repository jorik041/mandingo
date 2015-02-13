#!/usr/bin/env python

import pefile
import sys
import os
import re

def grepFileInfo(filePath):
	pe =  pefile.PE(filePath, fast_load=False)
	pe_info = ""
	for fileinfo in pe.FileInfo:
		if fileinfo.Key == 'StringFileInfo':
			for st in fileinfo.StringTable:
				for entry in st.entries.items():
					if len(entry[1].strip()):
						pe_info+="%s: %s\n" % (entry[0],entry[1])
	pe_info = re.sub(r'[^\x00-\x7F]', '#', pe_info)
	if len(pe_info):
		pe_info=pe_info[:len(pe_info)-1]
	return pe_info

if len(sys.argv)<2:
	print "Usage: %s <binary>" % sys.argv[0]
	sys.exit()

filename=sys.argv[1]

if not os.path.isfile(filename):
	print "File not found..."
	sys.exit()

print grepFileInfo(filename)


