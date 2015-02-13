#!/usr/bin/env python
# -*- coding: latin-1 -*-

import yara,sys,os

if len(sys.argv)==1:
	print "Provide some argument..."
	sys.exit()

filename=sys.argv[1]
found=False
def cb(data):
	global found
	if data["matches"]:
		found=True
		print data["meta"]["description"]
	yara.CALLBACK_CONTINUE

rules=yara.compile(os.path.dirname(os.path.abspath(__file__))+"/yara_rules/compiler.yar")
rules.match(filename,callback=cb)
if not found:
	print "Unknown"

