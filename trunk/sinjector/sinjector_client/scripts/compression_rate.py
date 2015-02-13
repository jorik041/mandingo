#!/usr/bin/env python

import zlib,sys,pefile

steps=100

if len(sys.argv)==1:
	print "Provide some argument..."
	sys.exit()

filename=sys.argv[1]


pe = pefile.PE(filename)

print "Name","PointerToRawData","SizeOfRawData","VirtualAddress","Flags","Entropy","Dir"

sections={}

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
	sections[name]={"name":name,"start":int(i.PointerToRawData),"size":int(i.SizeOfRawData)}

#[s_start]----[offset_s]------[s_end]
def sectionNamesIn(offset_s,offset_e):
	global sections
	name=""
	for s in sections:
		#check if section starts in the chunk (between offset_s and offset_e)
		if sections[s]["start"]>=offset_s and sections[s]["start"]<=offset_e:
			name+="%s " % s
		#check if part of the section is in the chunk
		if offset_s>=sections[s]["start"] and offset_s<=sections[s]["start"]+sections[s]["size"]:
			name+="%s " %s
	if offset_s and not len(name):
		name="padding?"
	return list(set(name.rstrip().split(" ")))

bytes=""
with open(filename,"rb") as f:
	bytes=f.read()

cbytes=zlib.compress(bytes)

lb=len(bytes)
lc=len(cbytes)
rate=1.0*lc/lb

DEBUG=False

if DEBUG:
	print "original size  :",lb
	print "compressed size:",lc
	print "rate           : %f" % rate

chunksize=lb/steps
if DEBUG:
	print "chunksize      : %d" % chunksize

markers=set()
for offset_s in range(0,lb,chunksize):
	chunk_end=offset_s+chunksize
	if chunk_end>lb:
		chunk_end=lb
	chunk=bytes[offset_s:chunk_end]
	c_chunk=zlib.compress(chunk)
	rate=1.0*len(c_chunk)/len(chunk)
	if rate>1:
		rate=1.0
	if DEBUG:
		print ">",offset_s,len(chunk),"->",len(c_chunk),"rate: %f" % rate
	else:
		sectionsFound=sectionNamesIn(offset_s,chunk_end)
		marker=""
		for sname in sectionsFound:
			if sname not in markers:
				markers.add(sname)
				marker+=sname+" "
		marker=marker.rstrip()
		secname=" ".join(sectionsFound)
		print "%s %s \"%s\" \"%s\"" % (offset_s,rate,secname,marker)

