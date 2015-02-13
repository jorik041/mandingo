#include <windows.h>
#include <stdio.h>
#include "inlinehook.h"
#include "..\includes\misc.h"

void BeginRedirect(LPVOID,LPVOID,DWORD*,BYTE *,BYTE *);                                        

#define SIZE 6
//#define POPUPS

typedef struct _hook{
	BOOL hooked;
	FARPROC pOrigMBAddress;                             // address of original
	DWORD oldMBProtect;  
	BYTE oldMBBytes[SIZE];                                         // backup
	BYTE MB_JMP[SIZE];                                              // 6 byte JMP instruction
	char *funcname;
}hook;

hook hooks[MAX_HOOKS];
int hookCount=0;

DWORD AddRedirect(char *libname,char *funcname,LPVOID newFunction){
	char msg[128];
	if(!hookCount) memset(hooks,0,sizeof(hooks));
	if(hookCount>=MAX_HOOKS) return 0;
	if(!GetModuleHandle(libname)) LoadLibrary(libname);
	hooks[hookCount].funcname=funcname;
	hooks[hookCount].pOrigMBAddress= (LPVOID) GetProcAddress(GetModuleHandle(libname),hooks[hookCount].funcname);
	hooks[hookCount].hooked=FALSE;
	//printf("%s %x\n",funcname,(int)hooks[hookCount].pOrigMBAddress);
	if(hooks[hookCount].pOrigMBAddress != NULL) {
		hooks[hookCount].hooked=TRUE;
		BeginRedirect(newFunction,hooks[hookCount].pOrigMBAddress,&hooks[hookCount].oldMBProtect,hooks[hookCount].oldMBBytes,hooks[hookCount].MB_JMP);
		hookCount++;
	}else{
		snprintf(msg,sizeof(msg),"Error - Function '%s' not found",funcname);
		#ifdef POPUPS
			MessageBoxA(0,msg,"AddRedirect",MB_OK);
		#endif
	}
	return (DWORD)hooks[hookCount].pOrigMBAddress;
}

void BeginRedirect(LPVOID newFunction,LPVOID origAddress,DWORD *oldProtect,BYTE *oldBytes,BYTE *JMP)  
{  
	BYTE tempJMP[SIZE] = {0xE9, 0x90, 0x90, 0x90, 0x90, 0xC3};         // 0xE9 = JMP 0x90 = NOP oxC3 = RET
	memcpy(JMP, tempJMP, SIZE);                                        // store jmp instruction to JMP
	DWORD JMPSize = ((DWORD)newFunction - (DWORD)origAddress - 5);  // calculate jump distance
	VirtualProtect((LPVOID)origAddress, SIZE,                       // assign read write protection
	       PAGE_EXECUTE_READWRITE, oldProtect);  
	memcpy(oldBytes, origAddress, SIZE);                            // make backup
	memcpy(&JMP[1], &JMPSize, 4);                              // fill the nop's with the jump distance (JMP,distance(4bytes),RET)
	memcpy(origAddress, JMP, SIZE);                                 // set jump instruction at the beginning of the original function
	VirtualProtect((LPVOID)origAddress, SIZE, *oldProtect, NULL);    // reset protection
}  
int findHook(char *funcname){
	int i;
	//for(i=0;i<hookCount;i++) if(!STRCMP(funcname,hooks[i].funcname)) return i;
	for(i=0;i<hookCount;i++) if(!strcmp(funcname,hooks[i].funcname)) return i;
	return -1;
}
FARPROC findProcAddr(char *funcname){
	int i=findHook(funcname);
	if(i!=-1) return hooks[i].pOrigMBAddress;
	return NULL;
}
int RestoreHook(char *funcname){
	char msg[128];
	int i=findHook(funcname);
	if(i==-1) {
		snprintf(msg,sizeof(msg),"Error - Function '%s' not found",funcname);
		#ifdef POPUPS
			MessageBoxA(0,msg,"RestoreHook",MB_OK);
		#endif
		return 1;
	}
	if(hooks[i].hooked==TRUE){
		VirtualProtect((LPVOID)hooks[i].pOrigMBAddress, SIZE, PAGE_EXECUTE_READWRITE, NULL);     // assign read write protection
		memcpy(hooks[i].pOrigMBAddress, hooks[i].oldMBBytes, SIZE);                            // restore backup
		VirtualProtect((LPVOID)hooks[i].pOrigMBAddress, SIZE, hooks[i].oldMBProtect, NULL);    // reset protection (yo)
		hooks[i].hooked=FALSE;
	}else{
		snprintf(msg,sizeof(msg),"Error - Function '%s' not hooked!",funcname);
		#ifdef POPUPS
			MessageBoxA(0,msg,"RestoreHook",MB_OK);
		#endif
	}
	return 0;
}
int HookAgain(char *funcname){
	char msg[128];
	int i=findHook(funcname);
	if(i==-1) {
		snprintf(msg,sizeof(msg),"Error - Function '%s' not found",funcname);
		#ifdef POPUPS
			MessageBoxA(0,msg,"HookAgain",MB_OK);
		#endif
		return 1;
	}
	if(hooks[i].hooked==FALSE){
		memcpy(hooks[i].pOrigMBAddress, hooks[i].MB_JMP, SIZE);                                 // set the jump instruction again
		VirtualProtect((LPVOID)hooks[i].pOrigMBAddress, SIZE, hooks[i].oldMBProtect, NULL);    // reset protection
		hooks[i].hooked=TRUE;
	}else{
		snprintf(msg,sizeof(msg),"Error - Function '%s' already hooked!",funcname);
		#ifdef POPUPS
			MessageBoxA(0,msg,"HookAgain",MB_OK);
		#endif
	}
	return 0;
}
