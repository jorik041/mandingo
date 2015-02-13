#!/usr/bin/env python

import string,sys,re

if len(sys.argv)==1:
	print "Provide some argument..."
	sys.exit()

data = open(sys.argv[1],'rb').read()

# Search for printable ASCII characters encoded as UTF-16LE.
pat = re.compile(ur'(?:[\x20-\x7E][\x00]){3,}')
words = [w.decode('utf-16le') for w in pat.findall(data)]
results=set()
for w in words:
	w=w.strip()
	if not len(w):
		continue
	w=re.sub("\s{5,}","     ",w)
	results.add(w)
	#print w

print "\n".join(list(results))
