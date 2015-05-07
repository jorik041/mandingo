import time,math

WHITE=(255,255,255)
BLUE =(0,0,255)
RED  =(255,0,0)

def f(i):
	global CAPTION,DISPLAYSURF
	DISPLAYSURF.fill(WHITE)
	CAPTION="frame "+str(i)
	#print i
	#sys.stdin.readline()	
	seconds=(time.time()-3)%60
	seconds_rad=seconds*2*math.pi/60+math.pi*1.6
	print seconds,seconds_rad
	r=DISPLAYSURF.get_bounding_rect()
	pygame.draw.line(DISPLAYSURF, BLUE, (r[2]/2, r[3]/2), (r[3]/2+math.cos(seconds_rad)*r[3], r[2]/2+math.sin(seconds_rad)*r[2]), 4)
	#pygame.draw.line(DISPLAYSURF, RED, (r[2]/2, r[3]/2), (math.cos(i/(2*math.pi))*r[2], math.sin(i/(2*math.pi))*r[2]), 4)
	time.sleep(.1)
#print "hola que tal"