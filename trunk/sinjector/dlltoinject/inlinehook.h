#pragma once

#define MAX_HOOKS 1024

DWORD AddRedirect(char *libname,char *funcname,LPVOID newFunction);
int RestoreHook(char *funcname);
int HookAgain(char *funcname);
FARPROC findProcAddr(char *funcname);
