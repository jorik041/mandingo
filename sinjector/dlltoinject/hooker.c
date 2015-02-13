#include "hooks_networking.h"
#include "hooks_registry.h"
#include "hooks_fileops.h"
#include "hooks_process.h"
#include "hooks_loadlib.h"
#include "hooker.h"

char iniFileName[512];
iniFile *ini=NULL;

VOID WINAPI MySleep(DWORD dwMilliseconds){
	if(dwMilliseconds){
		char tmp[32];
		snprintf(tmp,sizeof(tmp),"%d",dwMilliseconds);
		logger(ini,"Sleep",tmp,strlen(tmp));
	}
	RestoreHook("Sleep");
    Sleep(dwMilliseconds);
	HookAgain("Sleep");
}

void log_install(char *func,char *lib,char *name){
	char cmd[1024];
	sprintf(cmd,"\"%s\" \"%s\"",lib,name);
	logger(ini,func,cmd,strlen(cmd));
}
void AddRedirect_LOG(char *libname,char *funcname,LPVOID newFunction){
	if(ini->debuglevel>2) log_install("HOOKING",libname,funcname);
	AddRedirect(libname,funcname,newFunction);
}

#define HOOK_ENABLED 1

void hook(void){
	static BOOL hooked=FALSE;
	lockAccess(_getpid());
#if HOOK_ENABLED
	if(!hooked){
		//-----------------------------
		//File operation functions (11)
		//-----------------------------
		AddRedirect_LOG("kernel32.dll","CopyFileExA",MyCopyFileEx);
		AddRedirect_LOG("kernel32.dll","CopyFileExW",MyCopyFileExW);
		AddRedirect_LOG("kernel32.dll","CopyFileA",MyCopyFile);
		AddRedirect_LOG("kernel32.dll","CopyFileW",MyCopyFileW);
		AddRedirect_LOG("kernel32.dll","CreateFileA",MyCreateFile);
		AddRedirect_LOG("kernel32.dll","CreateFileW",MyCreateFileW);
		AddRedirect_LOG("kernel32.dll","DeleteFileW",MyDeleteFileW);
		AddRedirect_LOG("kernel32.dll","DeleteFileA",MyDeleteFile);
		AddRedirect_LOG("kernel32.dll","MoveFileExA",MyMoveFileEx);
		AddRedirect_LOG("kernel32.dll","MoveFileExW",MyMoveFileExW);
		AddRedirect_LOG("kernel32.dll","MoveFileA",MyMoveFile);
		AddRedirect_LOG("kernel32.dll","MoveFileW",MyMoveFileW);

		AddRedirect_LOG("kernel32.dll","Sleep",MySleep);
		//-----------------------
		//Registry functions (14)
		//-----------------------
		
		AddRedirect_LOG("advapi32.dll","RegCreateKeyExA",MyRegCreateKeyEx);
		AddRedirect_LOG("advapi32.dll","RegCreateKeyExW",MyRegCreateKeyExW);
		AddRedirect_LOG("advapi32.dll","RegCreateKeyA",MyRegCreateKey);
		AddRedirect_LOG("advapi32.dll","RegCreateKeyW",MyRegCreateKeyW);
		AddRedirect_LOG("advapi32.dll","RegOpenKeyExW",MyRegOpenKeyExW);
		AddRedirect_LOG("advapi32.dll","RegOpenKeyExA",MyRegOpenKeyExA);
		AddRedirect_LOG("advapi32.dll","RegOpenKeyW",MyRegOpenKeyW);
		AddRedirect_LOG("advapi32.dll","RegOpenKeyA",MyRegOpenKey);
		AddRedirect_LOG("advapi32.dll","RegQueryValueExA",MyRegQueryValueExA);
		AddRedirect_LOG("advapi32.dll","RegQueryValueExW",MyRegQueryValueExW);
		AddRedirect_LOG("advapi32.dll","RegSetValueExA",MyRegSetValueEx);
		AddRedirect_LOG("advapi32.dll","RegSetValueExW",MyRegSetValueExW);
		AddRedirect_LOG("advapi32.dll","RegConnectRegistryA",MyRegConnectRegistry);
		AddRedirect_LOG("advapi32.dll","RegConnectRegistryW",MyRegConnectRegistryW);
		
		//------------------------
		//networking functions (7)
		//------------------------
		AddRedirect_LOG("ws2_32.dll","connect",Myconnect);
		AddRedirect_LOG("ws2_32.dll","getaddrinfo",Mygetaddrinfo);
		AddRedirect_LOG("ws2_32.dll","GetAddrInfoW",MyGetAddrInfoW);
		AddRedirect_LOG("Wininet.dll","InternetConnectA",MyInternetConnect);
		AddRedirect_LOG("Wininet.dll","InternetConnectW",MyInternetConnectW);
		AddRedirect_LOG("Wininet.dll","HttpOpenRequestA",MyHttpOpenRequest);
		AddRedirect_LOG("Wininet.dll","HttpOpenRequestW",MyHttpOpenRequestW);
		//---------------------
		//Process functions (9)
		//---------------------
		AddRedirect_LOG("kernel32.dll","OpenProcess",MyOpenProcess);
		AddRedirect_LOG("kernel32.dll","CreateProcessA",MyCreateProcess);
		AddRedirect_LOG("kernel32.dll","CreateProcessW",MyCreateProcessW);
		AddRedirect_LOG("kernel32.dll","ResumeThread",MyResumeThread);
		AddRedirect_LOG("kernel32.dll","WriteProcessMemory",MyWriteProcessMemory);
		AddRedirect_LOG("kernel32.dll","CreateRemoteThread",MyCreateRemoteThread);
		AddRedirect_LOG("kernel32.dll","CreateProcessInternalA",MyCreateProcessInternal);
		AddRedirect_LOG("advapi32.dll","CreateProcessAsUserA",MyCreateProcessAsUser);
		AddRedirect_LOG("advapi32.dll","CreateProcessAsUserW",MyCreateProcessAsUserW);
		//---------------------
		//loadlib functions (4)
		//---------------------
		AddRedirect_LOG("kernel32.dll","GetModuleHandleA",MyGetModuleHandle);
		AddRedirect_LOG("kernel32.dll","GetModuleHandleW",MyGetModuleHandleW);
		AddRedirect_LOG("kernel32.dll","GetProcAddress",MyGetProcAddress);
		AddRedirect_LOG("kernel32.dll","LoadLibraryA",MyLoadLibrary);
		AddRedirect_LOG("kernel32.dll","LoadLibraryW",MyLoadLibraryW);
	}else{
		//---------------------
		//Process functions (9)
		//---------------------
		HookAgain("OpenProcess");
		HookAgain("CreateProcessA");
		HookAgain("CreateProcessW");
		HookAgain("WriteProcessMemory");
		HookAgain("CreateRemoteThread");
		HookAgain("ResumeThread");
		HookAgain("CreateProcessInternalA");
		HookAgain("CreateProcessAsUserA");
		HookAgain("CreateProcessAsUserW");
		//-----------------------------
		//File operation functions (10)
		//-----------------------------
		HookAgain("CopyFileA");
		HookAgain("CopyFileW");
		HookAgain("CopyFileExA");
		HookAgain("CopyFileExW");
		HookAgain("CreateFileA");
		HookAgain("CreateFileW");
		HookAgain("DeleteFileA");
		HookAgain("DeleteFileW");
		HookAgain("MoveFileExA");
		HookAgain("MoveFileExW");
		HookAgain("MoveFileA");
		HookAgain("MoveFileW");

		HookAgain("Sleep");
		//-----------------------
		//Registry functions (14)
		//-----------------------
		
		HookAgain("RegCreateKeyExA");
		HookAgain("RegCreateKeyExW");
		HookAgain("RegCreateKeyA");
		HookAgain("RegCreateKeyW");
		HookAgain("RegOpenKeyExW");
		HookAgain("RegOpenKeyExA");
		HookAgain("RegOpenKeyW");
		HookAgain("RegOpenKeyA");
		HookAgain("RegQueryValueExA");
		HookAgain("RegQueryValueExW");
		HookAgain("RegSetValueExA");
		HookAgain("RegSetValueExW");
		HookAgain("RegConnectRegistryA");
		HookAgain("RegConnectRegistryW");
		//------------------------
		//networking functions (7)
		//------------------------
		HookAgain("connect");
		HookAgain("getaddrinfo");
		HookAgain("GetAddrInfoW");
		HookAgain("InternetConnectA");
		HookAgain("InternetConnectW");
		HookAgain("HttpOpenRequestA");
		HookAgain("HttpOpenRequestW");
		//---------------------
		//loadlib functions (4)
		//---------------------
		HookAgain("GetModuleHandleA");
		HookAgain("GetModuleHandleW");
		HookAgain("GetProcAddress");
		HookAgain("LoadLibraryA");
		HookAgain("LoadLibraryW");
	}
#endif	
	printf("[hooker] Hooked\n");
}
//note: unhook it's not used in this version...
void unhook(void){
	unlockAccess(_getpid());
#if HOOK_ENABLED
	RestoreHook("LoadLibraryA"); 
//	RestoreHook("GetProcAddress");  
	RestoreHook("RegQueryValueExW");  
	RestoreHook("RegQueryValueExA");  
	RestoreHook("RegOpenKeyExW");  
	RestoreHook("RegOpenKeyExA");  
	RestoreHook("ShellExecuteExW");  
//	RestoreHook("VirtualAlloc");  
	RestoreHook("Sleep");  
	RestoreHook("DeleteFileA");  
	RestoreHook("DeleteFileW");  
	RestoreHook("CreateFileW");  
	RestoreHook("ResumeThread");
	RestoreHook("CreateRemoteThread");
	RestoreHook("WriteProcessMemory");
	RestoreHook("CreateProcessW");
	RestoreHook("CreateProcessA");
	RestoreHook("OpenProcess");
#endif
	printf("[hooker] Unhooked\n");
}

/*
HOOK_STUB_FUNCTION(MyLoadLibraryA,HINSTANCE,WINAPI,(LPCTSTR lpLibFileName)) {
	logger(ini,"LoadLibraryA",(char*)lpLibFileName,strlen(lpLibFileName));
	return HOOK_HOP(MyLoadLibraryA)(lpLibFileName);
}
HOOK_STUB_FUNCTION(MyGetProcAddress,FARPROC,WINAPI,(HMODULE hModule,LPCSTR lpProcName)) {
	logger(ini,"GetProcAddress",(char*)lpProcName,strlen(lpProcName));
	return HOOK_HOP(MyGetProcAddress)(hModule,lpProcName);
}

HOOK_STUB_FUNCTION(MyCreateFileW,HANDLE,WINAPI,(WCHAR *lpFileName,DWORD dwDesiredAccess,DWORD dwShareMode,LPSECURITY_ATTRIBUTES lpSecurityAttributes,DWORD dwCreationDisposition,DWORD dwFlagsAndAttributes,HANDLE hTemplateFile)){
	char fname[1024],cmd[1024],access[1024];
	*access=0;
	if(dwDesiredAccess&GENERIC_ALL) 	strncat(access,"GENERIC_ALL ",sizeof(access));
	if(dwDesiredAccess&GENERIC_READ) 	strncat(access,"GENERIC_READ ",sizeof(access));
	if(dwDesiredAccess&GENERIC_WRITE) 	strncat(access,"GENERIC_WRITE ",sizeof(access));
	if(dwDesiredAccess&GENERIC_EXECUTE)	strncat(access,"GENERIC_EXECUTE ",sizeof(access));
	wcstombs ( fname, lpFileName, sizeof(fname) );
	char *fullPath=getfile_fullpath(fname);
	sprintf(cmd,"CreateFileW (%sdwDesiredAccess=0x%x)",access,dwDesiredAccess);
	logger(ini,cmd,(char*)fullPath,strlen(fullPath));
	char msg[1024];
	strncpy(msg,"(you can dump the process now with external tools)\n\nlpFullPath: ",sizeof(msg));
	strncat(msg,fullPath,sizeof(msg));
	strncat(msg,"\n\nAllow this file action?\n\n",sizeof(msg));
	strncat(msg,cmd,sizeof(msg));
	if(ini && ini->monitor && StrStrI(fullPath,ini->monitor)){
		switch(MessageBox(NULL,msg,"File operation detected",MB_YESNO|MB_ICONASTERISK)){
			case IDYES:
				break;
			case IDNO:
				lpFileName=NULL;
				break;
		}
	}
	return HOOK_HOP(MyCreateFileW)(lpFileName,dwDesiredAccess,dwShareMode,lpSecurityAttributes,dwCreationDisposition,dwFlagsAndAttributes,hTemplateFile);
}
*/

iniFile *loadHookerIniFile(char *filename){
	strncpy(iniFileName,filename,sizeof(iniFileName));
	ini=parseIni(iniFileName);
	if(ini && ini->debuglevel){
		printf("[ini] monitor=%s\n",*ini->monitor?ini->monitor:"none (DISABLED)");
		printf("[ini] logfile=%s\n",*ini->logfile?ini->logfile:"none (DISABLED)");
		printf("[ini] debuglevel=%d\n",ini->debuglevel);
		printf("[ini] reinject=%d\n",ini->reinject);
	}
	return ini;
}
/*
UINT WINAPI fakeWinExec( LPCSTR lpCmdLine, UINT uCmdShow){
	logger(ini,"WinExec",NULL,0);
	return realWinExec(lpCmdLine,uCmdShow);
}
HANDLE WINAPI fakeCreateProcessInternal(HANDLE hToken,LPCTSTR lpApplicationName,LPTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFOA lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation,PHANDLE hNewToken){
	logger(ini,"CreateProcessInternal",NULL,0);
	return realCreateProcessInternal(hToken,lpApplicationName,lpCommandLine,lpProcessAttributes,lpThreadAttributes,bInheritHandles,dwCreationFlags,lpEnvironment,lpCurrentDirectory,lpStartupInfo,lpProcessInformation,hNewToken);
}
BOOL WINAPI fakeShellExecuteExW( SHELLEXECUTEINFO *pExecInfo){
	logger(ini,"ShellExecuteExW",NULL,0);
	return realShellExecuteExW(*pExecInfo);
}
BOOL WINAPI fakeShellExecuteExA( SHELLEXECUTEINFO *pExecInfo){
	logger(ini,"ShellExecuteExA",NULL,0);
	return realShellExecuteExA(*pExecInfo);
}
BOOL WINAPI fakeCreateProcessA(LPTSTR  lpApplicationName, LPTSTR  lpCommandLine, LPSECURITY_ATTRIBUTES  lpProcessAttributes, LPSECURITY_ATTRIBUTES  lpThreadAttributes, BOOL  bInheritHandles, DWORD  dwCreationFlags, LPVOID  lpEnvironment, LPTSTR  lpCurrentDirectory, LPSTARTUPINFOA  lpStartupInfo, LPPROCESS_INFORMATION  lpProcessInformation){
	char cmd[1024];
	sprintf(cmd,"\"%s\" \"%s\"",lpApplicationName,lpCommandLine);
	logger(ini,"CreateProcessA",cmd,strlen(cmd));
	return realCreateProcessA(lpApplicationName,lpCommandLine,lpProcessAttributes,lpThreadAttributes,bInheritHandles,dwCreationFlags,lpEnvironment,lpCurrentDirectory,lpStartupInfo,lpProcessInformation);
}
BOOL WINAPI fakeCreateProcessW(LPWSTR lpApplicationName,LPWSTR lpCommandLine, LPSECURITY_ATTRIBUTES  lpProcessAttributes, LPSECURITY_ATTRIBUTES  lpThreadAttributes, BOOL  bInheritHandles, DWORD  dwCreationFlags, LPVOID  lpEnvironment, LPWSTR  lpCurrentDirectory, LPSTARTUPINFOW  lpStartupInfo, LPPROCESS_INFORMATION  lpProcessInformation){
	char *appname=unicode2ascii(lpApplicationName);
	char *cmdline=unicode2ascii(lpCommandLine);
	char cmd[1024];
	sprintf(cmd,"\"%s\" \"%s\"",appname,cmdline);
	logger(ini,"CreateProcessW",cmd,strlen(cmd));
	return realCreateProcessW(lpApplicationName,lpCommandLine,lpProcessAttributes,lpThreadAttributes,bInheritHandles,dwCreationFlags,lpEnvironment,lpCurrentDirectory,lpStartupInfo,lpProcessInformation);
}
BOOL WINAPI fakeDeleteFileW(WCHAR *lpFilename){
	char *fname=unicode2ascii(lpFilename);
	logger(ini,"DeleteFileW",fname,strlen(fname));
	return realDeleteFileW(lpFilename);
}
BOOL WINAPI fakeCopyFileW(WCHAR *lpExistingFileName,WCHAR *lpNewFileName,  BOOL bFailIfExists){
	char cmd[1024];
	sprintf(cmd,"CopyFileW (lpExitingFileName=%s,lpNewFileName=%s)",unicode2ascii(lpExistingFileName),unicode2ascii(lpNewFileName));
	logger(ini,"CopyFileW",cmd,strlen(cmd));
	return realCopyFileW(lpExistingFileName,lpNewFileName,bFailIfExists);
}
HINSTANCE WINAPI fakeLoadLibrary(LPCTSTR lpLibFileName){
	logger(ini,"LoadLibraryA",(char*)lpLibFileName,strlen(lpLibFileName));
	return realLoadLibrary(lpLibFileName);
}
HINSTANCE WINAPI fakeLoadLibraryExA(LPCTSTR lpLibFileName){
	logger(ini,"LoadLibraryExA",(char*)lpLibFileName,strlen(lpLibFileName));
	return realLoadLibraryExA(lpLibFileName);
}
HINSTANCE WINAPI fakeLoadLibraryExW(LPWSTR lpLibFileName){
	char lib[1024];
	sprintf(lib,"%s",unicode2ascii(lpLibFileName));
	logger(ini,"LoadLibraryExA",(char*)lib,strlen(lib));
	return realLoadLibraryExW(lpLibFileName);
}
BOOL lockAccess(int pid){
	char mname[64];
	sprintf(mname,"inj_open_%d",pid);
	CreateMutex(NULL,TRUE,mname);
	return GetLastError()?FALSE:TRUE;
}

HANDLE WINAPI fakeCreateFileW(WCHAR *lpFileName,DWORD dwDesiredAccess,DWORD dwShareMode,LPSECURITY_ATTRIBUTES lpSecurityAttributes,
  	DWORD dwCreationDisposition,DWORD dwFlagsAndAttributes,HANDLE hTemplateFile){
	char fname[1024],cmd[1024],access[1024];
	*access=0;
	if(dwDesiredAccess&GENERIC_ALL) 	strncat(access,"GENERIC_ALL ",sizeof(access));
	if(dwDesiredAccess&GENERIC_READ) 	strncat(access,"GENERIC_READ ",sizeof(access));
	if(dwDesiredAccess&GENERIC_WRITE) 	strncat(access,"GENERIC_WRITE ",sizeof(access));
	if(dwDesiredAccess&GENERIC_EXECUTE)	strncat(access,"GENERIC_EXECUTE ",sizeof(access));
	wcstombs ( fname, lpFileName, sizeof(fname) );
	char *fullPath=getfile_fullpath(fname);
	sprintf(cmd,"CreateFileW (%sdwDesiredAccess=0x%x)",access,dwDesiredAccess);
	logger(ini,cmd,(char*)fullPath,strlen(fullPath));
	char msg[1024];
	strncpy(msg,"(you can dump the process now with external tools)\n\nlpFullPath: ",sizeof(msg));
	strncat(msg,fullPath,sizeof(msg));
	strncat(msg,"\n\nAllow this file action?\n\n",sizeof(msg));
	strncat(msg,cmd,sizeof(msg));
	if(ini && ini->monitor && StrStrI(fullPath,ini->monitor)){
		switch(MessageBox(NULL,msg,"File operation detected",MB_YESNO|MB_ICONASTERISK)){
			case IDYES:
				break;
			case IDNO:
				return 0;
				break;
		}
	}
    return realCreateFileW(lpFileName,dwDesiredAccess,dwShareMode,lpSecurityAttributes,dwCreationDisposition,dwFlagsAndAttributes,hTemplateFile);
}
HANDLE WINAPI fakeCreateFile(LPCTSTR lpFileName,DWORD dwDesiredAccess,DWORD dwShareMode,LPSECURITY_ATTRIBUTES lpSecurityAttributes,
  	DWORD dwCreationDisposition,DWORD dwFlagsAndAttributes,HANDLE hTemplateFile){
	logger(ini,"CreateFileA",(char*)lpFileName,strlen(lpFileName));
    return realCreateFile(lpFileName,dwDesiredAccess,dwShareMode,lpSecurityAttributes,dwCreationDisposition,dwFlagsAndAttributes,hTemplateFile);
}
HFILE WINAPI fakeOpenFile(LPCSTR lpFileName,LPOFSTRUCT lpReOpenBuff,UINT uStyle){
	logger(ini,"OpenFile",(char*)lpFileName,strlen(lpFileName));
    return realOpenFile(lpFileName,lpReOpenBuff,uStyle);
}
BOOL WINAPI fakeWriteFile(HANDLE hFile, LPCVOID lpBuffer, DWORD nNumberOfBytesToWrite, LPDWORD lpNumberOfBytesWritten, LPOVERLAPPED lpOverlapped)
{
	logger(ini,"WriteFile",(char*)lpBuffer,nNumberOfBytesToWrite);
    return realWriteFile(hFile, lpBuffer, nNumberOfBytesToWrite, lpNumberOfBytesWritten, lpOverlapped);;
}

BOOL WINAPI MyCreateProcess_(LPCTSTR lpApplicationName,LPTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFO lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation){
	char tmp[MAX_PATH];
	RestoreHook("OpenProcess");
	RestoreHook("CreateProcessA");
	BOOL suspended=FALSE;
	if(dwCreationFlags&CREATE_SUSPENDED) suspended=TRUE;//requested SUSPENDED flag
    BOOL r=CreateProcessA(lpApplicationName,lpCommandLine,lpProcessAttributes,lpThreadAttributes,bInheritHandles,(ini->reinject?CREATE_SUSPENDED:0)|dwCreationFlags,lpEnvironment,lpCurrentDirectory,lpStartupInfo,lpProcessInformation);
	DWORD lastError=GetLastError();
	int pid=(int)lpProcessInformation->dwProcessId;
	int handle=(int)lpProcessInformation->hProcess;
	if(!IsBadReadPtr(lpCommandLine,1) && !IsBadReadPtr(lpApplicationName,1)) {
		snprintf(tmp,sizeof(tmp),"PID=%d Handle=%d \"%s\" %s",pid,handle,lpApplicationName,lpCommandLine);
	}else if(!IsBadReadPtr(lpCommandLine,1)){
		snprintf(tmp,sizeof(tmp),"PID=%d Handle=%d \"\" %s",pid,handle,lpCommandLine);
	}else{
		snprintf(tmp,sizeof(tmp),"PID=%d Handle=%d \"\"",pid,handle);
	}
	logger(ini,"CreateProcessA",tmp,strlen(tmp));

	if(ini->reinject){
		//reinjection
		char cmd[1024];
		if(!pid && inBlackList(lpCommandLine,ini->reinject_blacklist) && lockAccess(lpProcessInformation->dwProcessId)){			
			sprintf(cmd,"\"C:\\Documents and Settings\\user\\My Documents\\Pelles C Projects\\dll_injection\\injector\\injector.exe\" /p %d  ",lpProcessInformation->dwProcessId);
//			if(MessageBox(0,cmd,"MyCreateProcess (reinject?)",MB_OKCANCEL)==MB_OK){			
				STARTUPINFO         sInfo;
				PROCESS_INFORMATION pInfo;
				ZeroMemory(&sInfo, sizeof(sInfo));
				sInfo.cb = sizeof(sInfo);
				ZeroMemory(&pInfo, sizeof(pInfo));
				logger(ini,"REINJECT",cmd,strlen(cmd));
				CreateProcessA(0,cmd, NULL, NULL, FALSE, NORMAL_PRIORITY_CLASS | CREATE_NO_WINDOW, NULL, NULL, &sInfo, &pInfo);
				WaitForSingleObject(pInfo.hProcess, INFINITE);
//			}
		}
		if(!suspended){
			WaitForSingleObject(lpProcessInformation->hProcess,5);
			ResumeThread(lpProcessInformation->hThread);
		}
	}
	HookAgain("CreateProcessA");
	HookAgain("OpenProcess");
	SetLastError(lastError);
	return r;
}
*/
