#!/usr/bin/env python
import dpkt
import socket
import sys

dumpId=None

if len(sys.argv)<2:
	print "Usage: %s <sinjector_logpath>" % sys.argv[0]
	sys.exit()

if len(sys.argv)>2:
	dumpId=int(sys.argv[2])

def parsePcap(filename,dump=None):
	count=1
	for ts, pkt in dpkt.pcap.Reader(open(filename,'r')):
		eth=dpkt.ethernet.Ethernet(pkt)
		if eth.type!=dpkt.ethernet.ETH_TYPE_IP:
			continue
		proto="?"
		if eth.ip.p==dpkt.ip.IP_PROTO_TCP:
			proto="TCP"
		if eth.ip.p==dpkt.ip.IP_PROTO_UDP:
			proto="UDP"
		ip=eth.data
		tcp_udp=ip.data

		if len(tcp_udp.data)==0:
			continue #do not show empty packets...

		if dump!=None and dump==count:
			sys.stdout.write(tcp_udp.data)
			sys.exit()
		count+=1
			
		someinfo=""
		host=""
		try:
			req=dpkt.http.Request(tcp_udp.data)
			someinfo=req.uri
			host=req.headers["host"]
		except:
			pass
		try:
			req=dpkt.http.Response(tcp_udp.data)
			someinfo=req.headers["content-type"]
		except:
			pass
		if tcp_udp.dport!=137: #do not extract netbios names
			try:
				dns=dpkt.dns.DNS(tcp_udp.data)
				for qd in dns.qd:
					host+=qd.name
				for answer in dns.an:
					try:
						someinfo+=socket.inet_ntoa(answer.rdata)+" "
					except Exception:
						pass
			except Exception,e:
				pass
		if dump==None:
			print proto,socket.inet_ntoa(eth.ip.src),tcp_udp.sport,socket.inet_ntoa(eth.ip.dst),tcp_udp.dport,len(tcp_udp.data),'"'+host+'"','"'+someinfo+'"'
	
parsePcap(sys.argv[1]+"/network.pcap",dumpId)
