#include "hooks_loadlib.h"

HMODULE WINAPI MyGetModuleHandle(LPCTSTR lpModuleName){
	RestoreHook("GetModuleHandleA");
	HMODULE r=GetModuleHandleA(lpModuleName);
	DWORD lastError=GetLastError();
	HookAgain("GetModuleHandleA");
	char temp[1024];
	snprintf(temp,sizeof(temp),"handle=%d \"%s\"",(int)r,lpModuleName);
	logger(ini,"GetModuleHandleA",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}

HMODULE WINAPI MyGetModuleHandleW(LPCWSTR lpModuleName){
	RestoreHook("GetModuleHandleW");
	HMODULE r=GetModuleHandleW(lpModuleName);
	DWORD lastError=GetLastError();
	HookAgain("GetModuleHandleW");
	char temp[1024],modulename[128];
	*modulename=0;
	if(!IsBadReadPtr(lpModuleName,1)) wcstombs ( modulename, lpModuleName, sizeof(modulename) );		
	snprintf(temp,sizeof(temp),"handle=%d \"%s\"",(int)r,modulename);
	logger(ini,"GetModuleHandleW",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}

FARPROC WINAPI MyGetProcAddress(HMODULE hModule,LPCSTR lpProcName){
	RestoreHook("GetProcAddress");  	
	FARPROC r=GetProcAddress(hModule,lpProcName);
	DWORD lastError=GetLastError();
	HookAgain("GetProcAddress");
	if(!IsBadReadPtr(lpProcName,1)) {
		char temp[1024];
		snprintf(temp,sizeof(temp),"handle=%d \"%s\"",(int)hModule,lpProcName);
		logger(ini,"GetProcAddress",temp,strlen(temp));
	}
	SetLastError(lastError);
	return r;
}

HMODULE WINAPI MyLoadLibrary(LPCTSTR lpFileName){
	RestoreHook("LoadLibraryA");  
   	HMODULE r= LoadLibraryA(lpFileName);
	DWORD lastError=GetLastError();
	if(!IsBadReadPtr(lpFileName,1)) {
		char temp[1024];
		snprintf(temp,sizeof(temp),"handle=%d \"%s\"",(int)r,lpFileName);
		logger(ini,"LoadLibraryA",(char*)temp,strlen(temp));
	}
	HookAgain("LoadLibraryA");
	SetLastError(lastError);
	return r;
}
HMODULE WINAPI MyLoadLibraryW(LPCWSTR lpFileName){
	RestoreHook("LoadLibraryW");  
   	HMODULE r= LoadLibraryW(lpFileName);
	DWORD lastError=GetLastError();
	char temp[1024];
	char filename[128];
	*filename=0;
	if(!IsBadReadPtr(lpFileName,1)) wcstombs ( filename, lpFileName, sizeof(filename) );
	snprintf(temp,sizeof(temp),"handle=%d \"%s\"",(int)r,filename);
	logger(ini,"LoadLibraryW",(char*)temp,strlen(temp));
	HookAgain("LoadLibraryW");
	SetLastError(lastError);
	return r;
}
