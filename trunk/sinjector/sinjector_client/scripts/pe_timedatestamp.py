#!/usr/bin/env python
# -*- coding: latin-1 -*-

import pefile,sys

if len(sys.argv)==1:
	print "Provide some argument..."
	sys.exit()

pe =  pefile.PE(sys.argv[1], fast_load=True)

print pe.NT_HEADERS.FILE_HEADER.TimeDateStamp
