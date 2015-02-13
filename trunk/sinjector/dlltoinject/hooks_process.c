#include "hooks_process.h"

BOOL WINAPI MyCreateProcessInternal(DWORD unknown1, LPCTSTR lpApplicationName, LPCTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFO lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation,DWORD unknown2){
	DWORD lastError=0;
	BOOL r=0;
	BOOL suspended=FALSE;
	FARPROC CreateProcessInternalA=findProcAddr("CreateProcessInternalA");
	if(CreateProcessInternalA==NULL) return FALSE;
	if(dwCreationFlags&CREATE_SUSPENDED) suspended=TRUE;//requested SUSPENDED flag
	RestoreHook("CreateProcessInternalA");
	r=CreateProcessInternalA( unknown1,  lpApplicationName,  lpCommandLine, lpProcessAttributes, lpThreadAttributes, bInheritHandles, dwCreationFlags, lpEnvironment, lpCurrentDirectory, lpStartupInfo, lpProcessInformation,unknown2);
	lastError=GetLastError();
	char *appname=NULL,*cmdline=NULL;
	char temp[1024];
	int pid=(int)lpProcessInformation->dwProcessId;
	int handle=(int)lpProcessInformation->hProcess;
	int thread=(int)lpProcessInformation->hThread;
	if(!IsBadReadPtr(lpCommandLine,1)) 		cmdline=(char*)lpCommandLine;
	if(!IsBadReadPtr(lpApplicationName,1))	appname=(char*)lpApplicationName;
	snprintf(temp,sizeof(temp),"PID=%d Handle=%d Thread=%d %s\"%s\" %s",pid,handle,thread,suspended?"SUSPENDED ":"",appname,cmdline);
	logger(ini,"CreateProcessInternalA",(char*)temp,strlen(temp));
	if(ini->reinject){
		RestoreHook("OpenProcess");
		RestoreHook("CreateProcessA");
		//reinjection
		char cmd[1024];
		if(pid && !inBlackList(cmdline,ini->reinject_blacklist) && lockAccess(pid)){			
			sprintf(cmd,"\""SINJECTOR"\" /%c %d  ",suspended?'P':'p',lpProcessInformation->dwProcessId);
			STARTUPINFO         sInfo;
			PROCESS_INFORMATION pInfo;
			ZeroMemory(&sInfo, sizeof(sInfo));
			sInfo.cb = sizeof(sInfo);
			ZeroMemory(&pInfo, sizeof(pInfo));
			logger(ini,"REINJECT",cmd,strlen(cmd));
			CreateProcessA(0,cmd, NULL, NULL, FALSE, NORMAL_PRIORITY_CLASS | CREATE_NO_WINDOW, NULL, NULL, &sInfo, &pInfo);
			WaitForSingleObject(pInfo.hProcess, INFINITE);
		}
		if(!suspended) {
			RestoreHook("ResumeThread");
			ResumeThread((HANDLE)thread);
			HookAgain("ResumeThread");
		}
		HookAgain("OpenProcess");
		HookAgain("CreateProcessA");
	}
	HookAgain("CreateProcessInternalA");
	SetLastError(lastError);
	return r;
}

BOOL WINAPI MyCreateProcessAsUser(HANDLE hToken,LPCTSTR lpApplicationName,LPTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFO lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation){
	BOOL suspended=FALSE;
	if(dwCreationFlags&CREATE_SUSPENDED) suspended=TRUE;//requested SUSPENDED flag
	RestoreHook("CreateProcessAsUserA");
	BOOL r=CreateProcessAsUserA( hToken, lpApplicationName, lpCommandLine, lpProcessAttributes, lpThreadAttributes, bInheritHandles, dwCreationFlags, lpEnvironment, lpCurrentDirectory, lpStartupInfo, lpProcessInformation);
	DWORD lastError=GetLastError();
	HookAgain("CreateProcessAsUserA");
	char *appname=NULL,*cmdline=NULL;
	char temp[1024];
	int pid=(int)lpProcessInformation->dwProcessId;
	int handle=(int)lpProcessInformation->hProcess;
	int thread=(int)lpProcessInformation->hThread;
	if(!IsBadReadPtr(lpCommandLine,1)) 		cmdline=(char*)lpCommandLine;
	if(!IsBadReadPtr(lpApplicationName,1))	appname=(char*)lpApplicationName;
	snprintf(temp,sizeof(temp),"PID=%d Handle=%d Thread=%d %s\"%s\" %s",pid,handle,thread,suspended?"SUSPENDED ":"",appname,cmdline);
	logger(ini,"CreateProcessAsUserA",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyCreateProcessAsUserW(HANDLE hToken,LPCWSTR lpApplicationName,LPWSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCWSTR lpCurrentDirectory,LPSTARTUPINFOW lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation){
	BOOL suspended=FALSE;
	if(dwCreationFlags&CREATE_SUSPENDED) suspended=TRUE;//requested SUSPENDED flag
	RestoreHook("CreateProcessAsUserW");
	BOOL r=CreateProcessAsUserW( hToken, lpApplicationName, lpCommandLine, lpProcessAttributes, lpThreadAttributes, bInheritHandles, dwCreationFlags, lpEnvironment, lpCurrentDirectory, lpStartupInfo, lpProcessInformation);
	DWORD lastError=GetLastError();
	HookAgain("CreateProcessAsUserW");
	char temp[1024],cmdline[MAX_PATH],appname[MAX_PATH];
	int pid=(int)lpProcessInformation->dwProcessId;
	int handle=(int)lpProcessInformation->hProcess;
	int thread=(int)lpProcessInformation->hThread;
	if(!IsBadReadPtr(lpCommandLine,1)) 		wcstombs ( cmdline, lpCommandLine, sizeof(cmdline) );
	if(!IsBadReadPtr(lpApplicationName,1))	wcstombs ( appname, lpApplicationName, sizeof(appname) );
	snprintf(temp,sizeof(temp),"PID=%d Handle=%d Thread=%d %s\"%s\" %s",pid,handle,thread,suspended?"SUSPENDED ":"",appname,cmdline);
	logger(ini,"CreateProcessAsUserW",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
UINT WINAPI MyWinExec(LPCSTR lpCmdLine,UINT uCmdShow){
	RestoreHook("WinExec");
	UINT r=WinExec( lpCmdLine, uCmdShow);
	DWORD lastError=GetLastError();
	HookAgain("WinExec");
	logger(ini,"WinExec",(char*)lpCmdLine,strlen(lpCmdLine));
	SetLastError(lastError);
	return r;
}
BOOL MyShellExecuteExW(LPSHELLEXECUTEINFOW pExecInfo){
	char fname[1024];
	wcstombs ( fname, pExecInfo->lpFile, sizeof(fname) );
	logger(ini,"ShellExecuteExW",(char*)fname,strlen(fname));
	RestoreHook("ShellExecuteExW");
	BOOL r=ShellExecuteExW(pExecInfo);
	DWORD lastError=GetLastError();
	HookAgain("ShellExecuteExW");
	SetLastError(lastError);
	return r;
}
HANDLE WINAPI MyOpenProcess(DWORD dwDesiredAccess,BOOL bInheritHandle,DWORD dwProcessId){
	char cmd[2048],access[512];
	char *cmdline;
	RestoreHook("OpenProcess");
    HANDLE r=OpenProcess(dwDesiredAccess,bInheritHandle,dwProcessId);
	DWORD lastError=GetLastError();
	if(dwProcessId){
		cmdline=getcmdlinefrompid(dwProcessId);
		*access=0;
		if(dwDesiredAccess&PROCESS_CREATE_PROCESS)    strncat(access,"PROCESS_CREATE_PROCESS ",sizeof(access));
		if(dwDesiredAccess&PROCESS_SET_QUOTA)         strncat(access,"PROCESS_SET_QUOTA ",sizeof(access));
		if(dwDesiredAccess&PROCESS_QUERY_INFORMATION) strncat(access,"PROCESS_QUERY_INFORMATION ",sizeof(access));
		if(dwDesiredAccess&PROCESS_VM_READ)	          strncat(access,"PROCESS_VM_READ ",sizeof(access));
		sprintf(cmd,"PID=%d Handle=%d (%sdwDesiredAccess=0x%x) %s",dwProcessId,(int)r,access,dwDesiredAccess,cmdline);
		logger(ini,"OpenProcess",cmd,strlen(cmd));
	}
	HookAgain("OpenProcess");
	SetLastError(lastError);
	return r;
}

BOOL WINAPI MyCreateProcess(LPTSTR lpApplicationName,LPTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFO lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation){
	char *appname=NULL,*cmdline=NULL;
	char tmp[MAX_PATH];
	RestoreHook("OpenProcess");
	RestoreHook("CreateProcessA");
	RestoreHook("CreateProcessInternalA");
	BOOL suspended=FALSE;
	if(dwCreationFlags&CREATE_SUSPENDED) suspended=TRUE;//requested SUSPENDED flag
    BOOL r=CreateProcessA(lpApplicationName,lpCommandLine,lpProcessAttributes,lpThreadAttributes,bInheritHandles,(ini->reinject?CREATE_SUSPENDED:0)|dwCreationFlags,lpEnvironment,lpCurrentDirectory,lpStartupInfo,lpProcessInformation);
	DWORD lastError=GetLastError();
	int pid=(int)lpProcessInformation->dwProcessId;
	int handle=(int)lpProcessInformation->hProcess;
	int thread=(int)lpProcessInformation->hThread;
	if(!IsBadReadPtr(lpCommandLine,1)) 		cmdline=lpCommandLine;
	if(!IsBadReadPtr(lpApplicationName,1))	appname=lpApplicationName;
	snprintf(tmp,sizeof(tmp),"PID=%d Handle=%d Thread=%d %s\"%s\" %s",pid,handle,thread,suspended?"SUSPENDED ":"",appname,cmdline);
	logger(ini,"CreateProcessA",tmp,strlen(tmp));
	if(ini->reinject){
		//reinjection
		char cmd[1024];
		if(pid && !inBlackList(cmdline,ini->reinject_blacklist) && lockAccess(pid)){			
			sprintf(cmd,"\""SINJECTOR"\" /%c %d  ",suspended?'P':'p',lpProcessInformation->dwProcessId);
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
		if(!suspended) {
			//WaitForSingleObject(handle,5);
			RestoreHook("ResumeThread");
			ResumeThread((HANDLE)thread);
			HookAgain("ResumeThread");
		}
	}
	HookAgain("CreateProcessInternalA");
	HookAgain("CreateProcessA");
	HookAgain("OpenProcess");
	SetLastError(lastError);
	return r;
}

BOOL WINAPI MyCreateProcessW(LPCWSTR lpApplicationName,LPWSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCWSTR lpCurrentDirectory,LPSTARTUPINFOW lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation){
	char appname[128],cmdline[1024];
	char tmp[MAX_PATH];
	RestoreHook("OpenProcess");
	RestoreHook("CreateProcessW");
	RestoreHook("CreateProcessInternalA");
	BOOL suspended=FALSE;
	if(dwCreationFlags&CREATE_SUSPENDED) suspended=TRUE;//requested SUSPENDED flag
    BOOL r=CreateProcessW(lpApplicationName,lpCommandLine,lpProcessAttributes,lpThreadAttributes,bInheritHandles,(ini->reinject?CREATE_SUSPENDED:0)|dwCreationFlags,lpEnvironment,lpCurrentDirectory,lpStartupInfo,lpProcessInformation);
	DWORD lastError=GetLastError();
	int pid=(int)lpProcessInformation->dwProcessId;
	int handle=(int)lpProcessInformation->hProcess;
	int thread=(int)lpProcessInformation->hThread;
	*cmdline=0;
	*appname=0;
	if(!IsBadReadPtr(lpCommandLine,1)) 		wcstombs ( cmdline, lpCommandLine, sizeof(cmdline) );
	if(!IsBadReadPtr(lpApplicationName,1))	wcstombs ( appname, lpApplicationName, sizeof(appname) );
	snprintf(tmp,sizeof(tmp),"PID=%d Handle=%d Thread=%d %s\"%s\" %s",pid,handle,thread,suspended?"SUSPENDED ":"",appname,cmdline);
	logger(ini,"CreateProcessW",tmp,strlen(tmp));
	if(ini->reinject){
		//reinjection
		char cmd[1024];
		wcstombs (cmd, lpCommandLine, sizeof(cmd) );
		if(pid && !inBlackList(cmd,ini->reinject_blacklist) && lockAccess(pid)){
			sprintf(cmd,"\""SINJECTOR"\" /%c %d  ",suspended?'P':'p',lpProcessInformation->dwProcessId);
//			if(MessageBox(0,cmd,"MyCreateProcess (reinject?)",MB_OKCANCEL)==MB_OK){			
				STARTUPINFO         sInfo;
				PROCESS_INFORMATION pInfo;
				ZeroMemory(&sInfo, sizeof(sInfo));
				sInfo.cb = sizeof(sInfo);
				ZeroMemory(&pInfo, sizeof(pInfo));
				logger(ini,"REINJECT",cmd,strlen(cmd));
				RestoreHook("CreateProcessA");
				CreateProcessA(0,cmd, NULL, NULL, FALSE, NORMAL_PRIORITY_CLASS | CREATE_NO_WINDOW, NULL, NULL, &sInfo, &pInfo);
				//WaitForSingleObject(pInfo.hProcess, INFINITE);
				WaitForInputIdle(pInfo.hProcess, INFINITE);
				HookAgain("CreateProcessA");
//			}
		}
		if(!suspended){
			//WaitForSingleObject(handle,5);
			RestoreHook("ResumeThread");
			ResumeThread((HANDLE)thread);
			HookAgain("ResumeThread");
		}else{
			WaitForInputIdle((HANDLE)handle,1000);
		}
	}
	HookAgain("CreateProcessInternalA");
	HookAgain("CreateProcessW");
	HookAgain("OpenProcess");
	SetLastError(lastError);
	return r;
}

DWORD WINAPI MyResumeThread(HANDLE hThread){
	char tmp[16];
	RestoreHook("ResumeThread");
	DWORD r=ResumeThread(hThread);
	DWORD lastError=GetLastError();
	HookAgain("ResumeThread");
	snprintf(tmp,sizeof(tmp),"%d",(int)hThread);
	logger(ini,"ResumeThread",tmp,strlen(tmp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyWriteProcessMemory(HANDLE hProcess,LPVOID lpBaseAddress,LPCVOID lpBuffer,SIZE_T nSize,SIZE_T *lpNumberOfBytesWritten){
	char tmp[128];
	if((int)hProcess!=-1){
		snprintf(tmp,sizeof(tmp),"PID=%d Handle=%d BaseAddr=0x%x lpBuffer=0x%x Size=%d",GetProcessId(hProcess),(int)hProcess,(int)lpBaseAddress,(int)lpBuffer,nSize);
		logger(ini,"WriteProcessMemory",tmp,strlen(tmp));
	}
	RestoreHook("WriteProcessMemory");
    BOOL r=WriteProcessMemory( hProcess, lpBaseAddress, lpBuffer, nSize, lpNumberOfBytesWritten);
	DWORD lastError=GetLastError();
	HookAgain("WriteProcessMemory");
	SetLastError(lastError);
	return r;
}
HANDLE WINAPI MyCreateRemoteThread(HANDLE hProcess,LPSECURITY_ATTRIBUTES lpThreadAttributes,SIZE_T dwStackSize,LPTHREAD_START_ROUTINE lpStartAddress,LPVOID lpParameter,DWORD dwCreationFlags,LPDWORD lpThreadId){
	char tmp[128];
	if((int)hProcess!=-1){
		snprintf(tmp,sizeof(tmp),"PID=%d Handle=%d StartAddr=0x%x",GetProcessId(hProcess),(int)hProcess,(int)lpStartAddress);
		logger(ini,"CreateRemoteThread",tmp,strlen(tmp));
	}
	if(ini->reinject){
		//reinjection
		char cmd[1024];
		int pid=GetProcessId(hProcess);
		//TODO: check process for not infect blacklist processes
		if(/*!inBlackList(lpCommandLine,ini->reinject_blacklist) && */lockAccess(pid)){			
			sprintf(cmd,"\""SINJECTOR"\" /p %d  ",pid);
//			if(MessageBox(0,cmd,"MyCreateRemoteThread (reinject?)",MB_OKCANCEL)==MB_OK){			
				STARTUPINFO         sInfo;
				PROCESS_INFORMATION pInfo;
				ZeroMemory(&sInfo, sizeof(sInfo));
				sInfo.cb = sizeof(sInfo);
				ZeroMemory(&pInfo, sizeof(pInfo));
				logger(ini,"REINJECT",cmd,strlen(cmd));
				RestoreHook("CreateProcessA");
				RestoreHook("CreateProcessInternalA");
				CreateProcessA(0,cmd, NULL, NULL, FALSE, NORMAL_PRIORITY_CLASS | CREATE_NO_WINDOW, NULL, NULL, &sInfo, &pInfo);
				HookAgain("CreateProcessInternalA");
				HookAgain("CreateProcessA");
				WaitForSingleObject(pInfo.hProcess, INFINITE);
//			}
		}
	}
	RestoreHook("CreateRemoteThread");
    HANDLE r=CreateRemoteThread( hProcess, lpThreadAttributes, dwStackSize, lpStartAddress, lpParameter, dwCreationFlags, lpThreadId);
	DWORD lastError=GetLastError();
	HookAgain("CreateRemoteThread");
	SetLastError(lastError);
	return r;
}
LPVOID WINAPI MyVirtualAlloc(LPVOID lpAddress,SIZE_T dwSize,DWORD flAllocationType,DWORD flProtect){
	char tmp[128];
	snprintf(tmp,sizeof(tmp),"Addr=0x%x Size=%d",(int)lpAddress,dwSize);
	logger(ini,"VirtualAlloc",tmp,strlen(tmp));
	RestoreHook("VirtualAlloc");
	LPVOID r=VirtualAlloc( lpAddress, dwSize, flAllocationType, flProtect);
	DWORD lastError=GetLastError();
	HookAgain("VirtualAlloc");
	SetLastError(lastError);
	return r;
}
