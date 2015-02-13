#!/usr/bin/env python

from SimpleXMLRPCServer import SimpleXMLRPCServer
from SimpleXMLRPCServer import SimpleXMLRPCRequestHandler
import subprocess
import os
import base64
import threading

# Restrict to a particular path.
class RequestHandler(SimpleXMLRPCRequestHandler):
    rpc_paths = ('/RPC2',)

# Create server
HOST="0.0.0.0"
PORT=8000
print "Listening on %s:%d" % (HOST,PORT)
server = SimpleXMLRPCServer((HOST,PORT),
                            requestHandler=RequestHandler, allow_none=True)
server.register_introspection_functions()

class Runner(threading.Thread):
    def __init__(self, function_runner,cmdline):
		threading.Thread.__init__(self)
		self.runnable = function_runner
		self.daemon = True
		self.cmdline=cmdline

    def run(self):
        self.runnable(self.cmdline)
		
def Runner_func(cmdline):
	print "Running: %s" % cmdline
	return subprocess.Popen(cmdline, shell=True, stdout=subprocess.PIPE).stdout.read()

def Run(cmdline,background=False):
	if background:
		thread = Runner(Runner_func,cmdline)
		thread.start()
	else:
		return Runner_func(cmdline)

server.register_function(Run)

def Upload(arg):
	outfile=os.getcwd()+"/binary4"
	print "Saving '%s' file" % outfile
	with open(outfile,"wb") as output:
		output.write(arg.data)
	output.close()
	return "File uploaded to: %s" % outfile
	
server.register_function(Upload)

def ReadFile(filename):
	print "%s requested" % filename
	res=""
	try:
		with open(filename,"rb") as _input:
			res=_input.read()
		_input.close()
	except Exception,e:
		res=str(e)
	return base64.b64encode(res)

server.register_function(ReadFile)

# Run the server's main loop
server.serve_forever()

