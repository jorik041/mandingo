#!/usr/bin/env python
import pefile,sys

if len(sys.argv)==1:
	print "Provide some argument..."
	sys.exit()

pe =  pefile.PE(sys.argv[1],fast_load=False)

print "Name","PointerToRawData","SizeOfRawData","VirtualAddress","Flags","Entropy","Dir"

for i in pe.sections:
	name=i.Name.replace('\x00', '')
	flags=""
	if i.IMAGE_SCN_CNT_CODE:
		flags="code,"
	elif i.IMAGE_SCN_CNT_INITIALIZED_DATA:
		flags="data,"
	if i.IMAGE_SCN_MEM_READ:
		flags+="read,"
	if i.IMAGE_SCN_MEM_WRITE:
		flags+="write,"
	if i.IMAGE_SCN_MEM_EXECUTE:
		flags+="execute,"
	if i.IMAGE_SCN_MEM_SHARED:
		flags+="shared,"
	if len(flags):
		flags=flags[:len(flags)-1]

	dirname=""
	for d in pe.OPTIONAL_HEADER.DATA_DIRECTORY:
		if i.VirtualAddress==d.VirtualAddress:
			dirname=d.name

	print name,hex(i.PointerToRawData),hex(i.SizeOfRawData),hex(i.VirtualAddress),flags,i.get_entropy(),dirname
