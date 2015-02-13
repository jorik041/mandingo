#!/usr/bin/env python

import pefile,sys

if len(sys.argv)==1:
	print "Provide some argument..."
	sys.exit()

pe =  pefile.PE(sys.argv[1])

pe.parse_data_directories()

for entry in pe.DIRECTORY_ENTRY_IMPORT:
  for imp in entry.imports:
    print entry.dll, imp.name, hex(imp.address)
