import pygame
from pygame.locals import *
 
def name():
	pygame.init()
	screen = pygame.display.set_mode((480, 360))
	name = ""
	font = pygame.font.Font(None, 30)
	clock = pygame.time.Clock()
	while True:
		for evt in pygame.event.get():
			if evt.type == KEYDOWN:
				if evt.unicode.isalpha():
					name += evt.unicode
				elif evt.key == K_BACKSPACE:
					name = name[:-1]
				elif evt.key == K_RETURN:
					if name=="quit": return
					name = ""
				elif evt.key == K_SPACE:
					name += " "
			elif evt.type == QUIT:
				return
		screen.fill ((0, 0, 0))
		block = font.render(name, True, (255, 255, 255))
		rect = block.get_rect()
		rect.center = screen.get_rect().center
		screen.blit(block, rect)
		pygame.display.flip()
		clock.tick(100)
 		pygame.display.set_caption("fps: " + str(clock.get_fps()))
if __name__ == "__main__":
	name()