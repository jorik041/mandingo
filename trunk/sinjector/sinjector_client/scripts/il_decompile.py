#!/usr/bin/env python

import sys
import os
import subprocess
import re

class method:
	def __init__(self):
		self.classname=""
		self.type=""
		self.ret=""
		self.name=""
		self.params=""
		self.line=""

class asm:
	def __init__(self):
		self.label=""
		self.opcode=""
		self.params=""
		self.line=""

class IL:
	def __init__(self,argv):
		self.argv=argv
		self.filename=None
		self.ilcode=None
		self.methods_raw=None
		self.methods=[]
		self.ops={"dumpMethod":None}
		self.parseOptions()
		self.loadIL()
		self.parseMethods()
		if self.ops["dumpMethod"]:
			self.dumpMethod()
		else:
			self.dump_root()
	def dumpMethod(self):
		found=False
		body=""
		code=""
		for line in self.ilcode.split("\n"):
			if len(re.findall("\.method.+%s" % self.ops["dumpMethod"],line)):
				found=True
			if not found:
				continue
			if len(re.findall("IL_[0-9a-f]+[:,\)]",line)):
				if not len(code):
					body+="//CODE_GOES_HERE\n"
				code+=line+"\n"
				continue
			if len(line):
				if len(re.findall("^\s*(\/\/|\.maxstack)",line)): #remove comments
					continue
				body+=line+"\n"
			if len(re.findall("end of method",line)):
				found=False
		if len(body):
			body=body.rstrip()
		if len(code):
			code=code.rstrip()
		if len(body):
			print body
		self.decompile(code)
	def decompile(self,code):
		codes=[]
		saved=""
		joinLine=False
		for line in code.split("\n"):
			if re.findall("switch",line):
				joinLine=True
			if joinLine:
				saved+=line.lstrip()
			if joinLine and len(re.findall("\)",line)):
				joinLine=False
				line=saved
				saved=""
				#print "saved:",saved
				#sys.exit()
			if joinLine:
				continue
			m=re.findall("(IL_[0-9a-f]+):\s+([^\s]+)\s?(.+)?",line)
			if len(m):
				a=asm()
				a.line  =line
				a.label =m[0][0]
				a.opcode=m[0][1]
				a.params=m[0][2]
				codes.append(a)
		index=0
		lastCall=""
		locales={}
		for c in codes:
			print "%s|%s|%s" % (c.label,c.opcode,c.params)
			if c.opcode=="bne.un.s":
				comp1=""
				comp2=""
				if codes[index-1].opcode=="ldc.i4.1":
					comp1="1"
				if codes[index-2].opcode=="call":
					comp2=codes[index-2].params
					comp2=re.sub(".+class ","",comp2)
				if len(comp1) and len(comp2):
					res="if(%s!=%s) goto %s" % (comp1,comp2,c.params)
					res=res.replace("(1!=","(!")
					print ">>\033[33;1m",res,"\033[0m"
			if c.opcode in("call","callvirt","newobj"):
				m=re.findall(".+?\((.+)\)",c.params)
				params=""
				found=False
				if len(m):
					params=m[0]
				if params in ("string","string, string","string, bool","int32"):
					string1=""
					string2=""
					bool1=""
					int32_1=""
					found=True
					if params in ("string","string, string"):
						if codes[index-1].opcode[:6]=="ldloc.":
							m=re.findall("ldloc\.(\d+)",codes[index-1].opcode)
							if len(m):
								string1=locales[int(m[0])]
						if codes[index-1].opcode=="ldstr":
							string1=codes[index-1].params
						if codes[index-2].opcode=="ldstr":
							string2=codes[index-2].params
						if codes[index-2].opcode[:6]=="ldloc.":
							m=re.findall("ldloc\.(\d+)",codes[index-2].opcode)
							if len(m):
								string2=locales[int(m[0])]
					if params in ("string, bool"):
						if codes[index-2].opcode=="ldstr":
							string2=codes[index-2].params
						m=re.findall("ldc\.i4\.(\d+)",codes[index-1].opcode)
						if len(m):
							if m[0][0]=="0":
								bool1="False"
							else:
								bool1="True"
					if params in ("int32"):
						if codes[index-1].opcode=="ldc.i4":
							int32_1=codes[index-1].params
					if len(string1):
						res=c.params
						if res=="string string::Concat(string, string)":
							res="string+string"
						res=re.sub("::'\.ctor'","",res)
						res=re.sub("^.+\[mscorlib\]","",res)
						res=re.sub("^.+?class ","",res)
						res=re.sub("^string ","",res)
						#res=re.sub("\[mscorlib\]","",res)
						res=re.sub("string::","str1ng::",res)
						res=re.sub("^string ","str1ng ",res)
						res=re.sub("bool::","b0ol::",res)
						res=re.sub("^bool ","b0ol ",res)
						if c.opcode in ("call"):
							if len(string2):
								res=res.replace("string",string2,1) #replace param2
							res=res.replace("string",string1,1) #replace param1
						else:
							res=res.replace("string",string1,1) #replace param1
							if len(string2):
								res=res.replace("string",string2,1) #replace param2
						if len(bool1):
							res=res.replace("bool",bool1,1) #replace param2
						res=re.sub("str1ng","string",res)
						res=re.sub("b0ol","bool",res)
						lastCall=res
						print ">>\033[33;1m",res,"\033[0m"
					elif len(int32_1):
						res=c.params
						res=re.sub("^.+\[mscorlib\]","",res)
						res=res.replace("int32",int32_1,1) #replace param1
						lastCall=res
						print ">>\033[33;1m",res,"\033[0m"
					else:
						res=c.params
						res=re.sub("^.+\[mscorlib\]","",res)
						lastCall=res
						print "?? \033[31mmissing string1? params:",params,"\033[0m"
				if not found:
					res=c.params
					res=re.sub("^.+?\[mscorlib\]","",res)
					lastCall=res
					if not len(params):
						print ">>\033[33;1m",res,"\033[0m"
					else:
						print "?? \033[31mparams: Unknown\033[0m"
			if c.opcode[:6]=="stloc.":
				m=re.findall("stloc\.(\d+)",c.opcode)
				if len(m):
					local=int(m[0])
					locales[local]=lastCall
					if codes[index-1].opcode in ("call","callvirt","newobj"):
						res="V_%d=%s" % (local,lastCall)
						print ">>\033[33;1m",res,"\033[0m"					
			if c.opcode=="brtrue.s":
				comp=""
				if codes[index-1].opcode in ("call","callvirt"):
					comp=lastCall
				if len(comp):
					res="if(%s) goto %s" % (comp,c.params)
					print ">>\033[33;1m",res,"\033[0m"
					lastCall=""
			if c.opcode in("newarr"):
				size=""
				m=re.findall("ldc\.i4\.(\d+)",codes[index-1].opcode)
				if len(m):
					size=m[0][0]
				if codes[index-1].opcode=="ldc.i4.s":
					size=codes[index-1].params
				if len(size):
					res=c.params
					res=re.sub("\[mscorlib\]","",res)
					res+="[%s]" % int(size,16)
					print ">>\033[33;1m",res,"\033[0m"
			if c.opcode in("stelem.i1"):
				value=""
				m=re.findall("ldc\.i4\.(\d+)",codes[index-1].opcode)
				if len(m):	
					value=m[0][0]
				if codes[index-1].opcode in ("ldc.i4.s"):	
					value=codes[index-1].params
				if len(value):
					idx=""
					n=re.findall("ldc\.i4\.(\d+)",codes[index-2].opcode)
					if len(n):
						idx=n[0][0]
					if codes[index-2].opcode in ("ldc.i4.s"):	
						idx=codes[index-2].params
					if len(idx):
						res="array[%d]=%d" % (int(idx,16),int(value,16))
						print ">>\033[33;1m",res,"\033[0m"
						#print ">>\033[33;1m",res,"\033[0m"
			index+=1
	def dump_root(self):
		for m in self.methods:
			if m.name=="'.ctor'" or m.name=="'.cctor'":
				continue
			#print m.line
			print "%s.%s():%s" % (m.classname,m.name,m.ret)
	def parseMethods(self):
		classname="root"
		joinLine=False
		for line in self.methods_raw.split("\n"):
			if not len(line):
				continue
			if line[0]=="#":
				m=re.findall("[^\s]+\s(.+)",line)
				classname=m[0]
			elif re.findall(":",line):
				f=self.parseMethod(line)
				m=method()
				m.classname=classname
				m.type=f["d_type"]
				m.ret=f["d_ret"]
				m.name=f["d_name"].rstrip()
				m.params=f["d_params"]
				m.line=line
				if m.name in("BeginInvoke","EndInvoke","Invoke"):
					continue
				self.methods.append(m)
				#print ">>[%s]\t%s" % (classname,f["line"])
				#print "\ttype:%s\n\treturn:%s\n\tname:%s\n\tparams:%s" % (f["d_type"],f["d_ret"],f["d_name"],f["d_params"])
	def parseMethod(self,line):
		p=line.split("(")
		m=re.findall("\d+?:\s+([^\s]+) (.+) (.+)",p[0])
		d_type="?"
		d_ret="?"
		d_name="?"
		d_params="?"
		if len(m)>0:
			d_type=m[0][0]
			d_ret=m[0][1]
			d_name=m[0][2]
			pp=p[1].split(")")
			d_params=pp[0]
		return dict(line=line,d_type=d_type,d_ret=d_ret,d_name=d_name,d_params=d_params)
	def loadIL(self):
		self.ilcode=subprocess.Popen("monodis %s" % self.filename, shell=True, stdout=subprocess.PIPE).stdout.read()
		self.methods_raw=subprocess.Popen("monodis --method %s" % self.filename, shell=True, stdout=subprocess.PIPE).stdout.read()
		#join methods
		il=""
		savedline=""
		for line in self.ilcode.split("\n"):
			if len(re.findall("\.method",line)) and not len(re.findall("\(\)",line)):
				savedline+=line
				continue
			if len(savedline):
				il+=savedline+line.lstrip()+"\n"
				savedline=""
			else:
				il+=line+"\n"
		self.ilcode=il
	def parseOptions(self):
		if len(self.argv)==1:
			print "Provide some argument..."
			sys.exit()
		if os.path.isfile(self.argv[1]):
			self.filename=self.argv[1]
		if not self.filename:
			print "Input file not found..."
			sys.exit()
		for i in range(1,len(self.argv)):
			if self.argv[i]=="-m":
				self.ops["dumpMethod"]=self.argv[i+1]

if __name__ == "__main__":
	il=IL(sys.argv)

