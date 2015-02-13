#!/usr/bin/env python

import string,sys,re

def strings(filename, min=4):
	with open(filename, "rb") as f:
		result = ""
		data=f.read()
		for c in data:
			if c in string.printable:
				result += c
				continue
			if len(result) >= min:
				if len(result.strip()):
					result=result.strip()
					yield result
			result = ""
		if len(result)>=min:
			yield result

if len(sys.argv)==1:
	print "Provide some argument..."
	sys.exit()

for s in strings(sys.argv[1]):
	print s
