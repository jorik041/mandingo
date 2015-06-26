import pygame
from pygame.locals import *
from pygame.color import *

class box(pygame.sprite.Sprite):
    def __init__(self, x, y, largo, alto):
        pygame.sprite.Sprite.__init__(self)
        self.image = pygame.Surface([largo, alto])
        self.image.fill(pygame.Color("BLUE"))
        self.rect = self.image.get_rect()
        self.rect.y = y
        self.rect.x = x

def main():
	pygame.init()
	screen = pygame.display.set_mode((600, 600), pygame.DOUBLEBUF)
	clock = pygame.time.Clock()
	
	sprites = pygame.sprite.Group()
	sprites.add(box(0,0,10,100))

	running=True
	while running:
		for event in pygame.event.get():
			if event.type == QUIT:
				running = False
			elif event.type == KEYDOWN and event.key == K_ESCAPE:
				running = False
		### Flip screen
		sprites.draw(screen)
		pygame.display.flip()
		clock.tick(30)
		pygame.display.set_caption("fps: " + str(clock.get_fps()))

if __name__ == '__main__':
	main()	