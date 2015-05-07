#!/usr/bin/python

import sys,os
import pygame, sys
from pygame.locals import *

CAPTION='Hello World!'

pygame.init()
DISPLAYSURF = pygame.display.set_mode((400, 300))

def f(n):
	pass	

if len(sys.argv)<2 or not os.path.isfile(sys.argv[1]):
	print "Usage: %s <other.py>" % sys.argv[0]
	sys.exit()

sourceCode_filename=sys.argv[1]

i=0
while True:
	sourceCode=open(sourceCode_filename,"rt").read()
	try:
		exec sourceCode
		f(i)
	except Exception,e:
		print "[error] %d: %s" % (i,str(e))
	i+=1
	pygame.display.set_caption(str(CAPTION))
	for event in pygame.event.get():
		if event.type == QUIT:
			pygame.quit()
			sys.exit()
	pygame.display.update()
