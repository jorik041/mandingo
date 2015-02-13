#include <stdio.h>
#include <process.h>
#include <stdlib.h>
#include <windows.h>
#include "hooker.h"
#include "../includes/logger.h"

#define DEBUG_ATTACH 0

BOOL hooked=FALSE;
iniFile *pini=NULL;

INT APIENTRY DllMain(HMODULE hDLL, DWORD Reason, LPVOID Reserved) {
	switch(Reason) {
		case DLL_PROCESS_ATTACH:
			DisableThreadLibraryCalls(hDLL);
			pini=loadHookerIniFile(SINJECTOR_INI);
			//MessageBox(0,"Ready to hook","",MB_OK);
			hook();
#if DEBUG_ATTACH
			printf("[%-6d] DLL process attach function called\n",_getpid());
#endif
			break;
		case DLL_PROCESS_DETACH:
#if DEBUG_ATTACH
			printf("[%-6d] DLL process detach function called\n",_getpid());
#endif
			break;
/*		case DLL_THREAD_ATTACH:
#if DEBUG_ATTACH
			printf("[%-6d] DLL thread attach function called\n",_getpid());
#endif
			break;
		case DLL_THREAD_DETACH:
#if DEBUG_ATTACH
			printf("[%-6d] DLL thread detach function called\n",_getpid());
#endif
			break;*/
	}
	return TRUE;
}
