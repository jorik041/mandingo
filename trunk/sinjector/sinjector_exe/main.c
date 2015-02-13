#include <windows.h>
#include <stdio.h>
#include <process.h>
#include "listprocesses.h"
#include "../includes/inifile.h"
#include "../includes/misc.h"
#include "../includes/logger.h"
#include <Shellapi.h>
#include <stdlib.h>

#define INIFILE "injector.ini"
#define TITLE "Simple DLL Injector v1.01 by mandingo - Dic, 2014"

void usage(char *dllToInject,char *inifilepath){
	printf(TITLE"\n");
	printf("Usage         : injector <options>\n");
	printf("Options       : /l       List processes\n");
	printf("                /p <pid> Hook process with PID <pid> (/P if suspended)\n");
	//printf("                /u <pid> UnHook process with PID <pid>\n");
	//printf("                /i <pid> Dump process IAT\n");
	printf("                /x <cmd> Exec cmd (full path req.) and hook it\n");
	printf("                /X <cmd> Exec cmd, hook it and wait for keypress\n");
	printf("Configuration : %s\n",inifilepath);
	printf(" > settings   : dll=, monitor=, logfile=, iatfile=, debuglevel=\n");
	printf("                backup=, reinject=, reinject_blacklist=\n");
	printf("DLL Injected  : %s\n",dllToInject);
	exit(0);
}

//program options
typedef struct {
	BOOL dumpIAT;
	BOOL hook;
	BOOL loadINI;
	BOOL unhook;
	BOOL waitKeyPress;
	char *cmdline;
	char *iniPath;
	iniFile *ini;
	BOOL suspended;
}opt;
#include <TlHelp32.h>
void SetDebugPrivileges(void)
{
	void* tokenHandle;
	OpenProcessToken(GetCurrentProcess(), TOKEN_ADJUST_PRIVILEGES | TOKEN_QUERY, &tokenHandle);
	TOKEN_PRIVILEGES privilegeToken;
	LookupPrivilegeValue(0, SE_DEBUG_NAME, &privilegeToken.Privileges[0].Luid);
	privilegeToken.PrivilegeCount = 1;
	privilegeToken.Privileges[0].Attributes = SE_PRIVILEGE_ENABLED;
	AdjustTokenPrivileges(tokenHandle, 0, &privilegeToken, sizeof(TOKEN_PRIVILEGES), 0, 0);
	CloseHandle(tokenHandle);
}
void injecta_dll(DWORD pid,char *dllname,opt *options){
	//Classic DLL Injection
	//Get process handle passing in the process ID.
	HANDLE process = OpenProcess(MAXIMUM_ALLOWED, FALSE, pid);
	if(process == NULL) {
		printf("Error: the specified process couldn't be found\n");
		printf("PID: %d Last error: %d\n",pid,GetLastError());
		return;
	}
	 
	//Get address of the LoadLibrary function.
	LPVOID addrLoadLib = (LPVOID)GetProcAddress(GetModuleHandle("kernel32.dll"), "LoadLibraryA");
	if(addrLoadLib == NULL) {
	printf("Error: the LoadLibraryA function was not found inside kernel32.dll library\n");
	}
	 
	//Allocate new memory region inside the process's address space.
	LPVOID arg = (LPVOID)VirtualAllocEx(process, NULL, strlen(dllname), MEM_RESERVE | MEM_COMMIT, PAGE_READWRITE);
	if(arg == NULL) {
	printf("Error: the memory could not be allocated inside the chosen process\n");
	}
	 
	//Write the argument to LoadLibraryA to the process's newly allocated memory region.
	int n = WriteProcessMemory(process, arg, dllname, strlen(dllname), NULL);
	if(n == 0) {
	printf("Error: there was no bytes written to the process's address space\n");
	}
	 
	//Inject our DLL into the process's address space.
	//printf("Waiting for process (if it's suspended...)\n");   
	//WaitForInputIdle(process,INFINITE);

	HANDLE threadID = CreateRemoteThread(process, NULL, 0, (LPTHREAD_START_ROUTINE)addrLoadLib, arg, 0, NULL);
	if(threadID == NULL) {
	printf("[error] The remote thread could not be created\n");
	}
	else {
	printf("[info] The remote thread was successfully created\n");
	}
	 
	//Close the handle to the process, becuase we've already injected the DLL.
	CloseHandle(process);
}
void injecta_apc(DWORD pid,char *dllname,opt *options) {
	//MessageBox(0,"injecta_apc","",MB_OK);
    HANDLE hProcess = OpenProcess (MAXIMUM_ALLOWED,0,pid);
	//WaitForInputIdle(hProcess,2000);//fix some bugs on REINJECT
	HANDLE hThreadSnap = CreateToolhelp32Snapshot(TH32CS_SNAPTHREAD, 0);
	BOOL threadFound=FALSE;
	if (hThreadSnap != INVALID_HANDLE_VALUE) {
		THREADENTRY32 th32;
		th32.dwSize = sizeof(THREADENTRY32);
		BOOL bOK = TRUE;
		for (bOK = Thread32First(hThreadSnap, &th32); bOK; bOK = Thread32Next(hThreadSnap, &th32)) {
			if (th32.th32OwnerProcessID == pid) {
				//printf("[info] DLL Injected into thread ID %d\n",th32.th32ThreadID);
				HANDLE h = OpenThread (THREAD_ALL_ACCESS , 0, th32.th32ThreadID);
				if(h){
					threadFound=TRUE;
					HANDLE addr = (HANDLE)GetProcAddress(GetModuleHandle("kernel32.dll"), "LoadLibraryA");
					//Allocate new memory region inside the process's address space.
					LPVOID arg = (LPVOID)VirtualAllocEx(hProcess, NULL, strlen(dllname), MEM_RESERVE | MEM_COMMIT, PAGE_READWRITE);
					if(arg == NULL) {
					printf("Error: the memory could not be allocated inside the chosen process\n");
					}
					 
					//Write the argument to LoadLibraryA to the process's newly allocated memory region.
					int n = WriteProcessMemory(hProcess, arg, dllname, strlen(dllname), NULL);
					if(n == 0) {
					printf("Error: there was no bytes written to the process's address space\n");
					}
					QueueUserAPC((HANDLE)addr, h, (DWORD)arg);
					//MessageBox(0,dllname,"APC Injection done",MB_OK);
					//printf("[error] QueueUserAPC. Code: %d\n",GetLastError());
				}else{
					printf("[error] OpenThread. Code: %d\n",GetLastError());
				}
				CloseHandle(h);
				//break;
			}
		}
		CloseHandle(hThreadSnap);
	}
	CloseHandle(hProcess);
	if(!threadFound) printf("[ERROR] THREAD NOT FOUND!\n");
}
void injecta(DWORD pid,char *dllname,opt *options) {
	if(options->suspended){ 
		printf("[info] APC Injection\n");
		injecta_apc(pid,dllname,options);
	}else{
		printf("[info] CreateRemoteThread Injection\n");
		injecta_dll(pid,dllname,options);
	}
}

void spwanAndHook(char *dlltoinject,opt *options){
	STARTUPINFO         sInfo;
	PROCESS_INFORMATION pInfo;

	printf("[Info] Launching process: %s\n",options->cmdline);
	ZeroMemory(&sInfo, sizeof(sInfo));
	sInfo.cb = sizeof(sInfo);
	ZeroMemory(&pInfo, sizeof(pInfo));

	if (CreateProcess(options->cmdline, NULL, NULL, NULL, FALSE, CREATE_SUSPENDED, NULL, NULL, &sInfo, &pInfo))
	{
		char cmd[512];
		printf("[info] New pid: %d\n",pInfo.dwProcessId);
		sprintf(cmd,"EXECUTING \"%s\" HOOKING PID %d",options->cmdline,pInfo.dwProcessId);
		logger(options->ini,"injector",cmd,strlen(cmd));
		if(WaitForInputIdle((void*)pInfo.hProcess,1000)==WAIT_FAILED) {
			printf("[info] Wait failed, console app? good luck :p\n");
			injecta(pInfo.dwProcessId,dlltoinject,options);
			Sleep(1000);
		}else{
			injecta(pInfo.dwProcessId,dlltoinject,options);
		}
		if(options->waitKeyPress){
			printf("Press [intro] to resume process..\n");
			getchar();
		}
		ResumeThread((void*)pInfo.hThread);
	    CloseHandle(pInfo.hThread);
	    CloseHandle(pInfo.hProcess);
	}else{
		DWORD dwLastError=GetLastError();
		char lpBuffer[256];
	    FormatMessage(FORMAT_MESSAGE_FROM_SYSTEM,                 // It´s a system error
	                     NULL,                                      // No string to be formatted needed
	                     dwLastError,                               // Hey Windows: Please explain this error!
	                     MAKELANGID(LANG_NEUTRAL,SUBLANG_DEFAULT),  // Do it in the standard language
	                     lpBuffer,              // Put the message here
	                     sizeof(lpBuffer),                     // Number of bytes to store the message
	                     NULL);
		printf("[Error] Code %d - %s",GetLastError(),lpBuffer);
	}
}

int main(int argc, char* argv[]) {
	char dllToInject[512],iniFilePath[512];
	char cmd[512];
	DWORD pid=0;
	opt options;
	FILE *in;

	SetDebugPrivileges();
	//printf("%s\n",getfile_fullpath("\\\\.\\pipe\\mojo.6136.4468.15747523823731339023"));
	//return 1;
	char basePath[512];
	_fullpath(basePath, argv[0], sizeof(basePath));
	for(char *p=basePath+strlen(basePath);p>basePath;p--){
		if(*p=='\\') {
			*(p+1)=0;
			break;
		}
	}
	//build ini path
	strcpy(iniFilePath,basePath);
	strcat(iniFilePath,INIFILE);

	options.dumpIAT = FALSE;
	options.loadINI = TRUE;
	options.hook    = TRUE;
	options.unhook  = FALSE;
	options.cmdline = NULL;
	options.waitKeyPress = FALSE;
	options.iniPath = iniFilePath;
	options.suspended=FALSE;

	iniFile *ini=parseIni(iniFilePath);
	options.ini=ini;

	//build dll path
	strcpy(dllToInject,basePath);
	strcat(dllToInject,ini->dll);

	if(argc<2) usage(dllToInject,iniFilePath);

	in=fopen(dllToInject,"r");
	if(in==NULL){
		sprintf(cmd,"Error: DLL to inject NOT FOUND: %s",dllToInject);
		logger(ini,"injector",cmd,strlen(cmd));
		printf("DLL to inject not found... Path:\n");
		printf("%s\n",dllToInject);
		return 0;
	}
	fclose(in);

	if(argc>1 && argv[1][0]=='/'){
		//list processes
		if(argv[1][1]=='?' || argv[1][1]=='h') usage(dllToInject,iniFilePath);
		if(argv[1][1]=='l'){
			listProcesses();
			exit(0);
		}
		//read command line
		if(argv[1][1]=='x' || argv[1][1]=='X'){
			options.cmdline=argv[2];
			options.waitKeyPress=argv[1][1]=='X'?TRUE:FALSE;
		}
		//read the pid
		if(argv[1][1]=='p' || argv[1][1]=='P' || argv[1][1]=='i' || argv[1][1]=='u'){
			pid=atoi(argv[2]);
			if(argv[1][1]=='i') {
				options.dumpIAT=TRUE;
				options.hook=FALSE;
				sprintf(cmd,"Dump IAT requested for Pid %d",pid);
				logger(ini,"injector",cmd,strlen(cmd));
			}
			if(argv[1][1]=='u') {
				options.dumpIAT=FALSE;
				options.hook=FALSE;
				options.loadINI=FALSE;
				options.unhook=TRUE;
				sprintf(cmd,"Unhook requested for PID %d",pid);
				logger(ini,"injector",cmd,strlen(cmd));
			}
			if(argv[1][1]=='P') options.suspended=TRUE;
		}
	}

	printf(TITLE"\n");

	if(pid==0 && options.cmdline==NULL) return 1;

	if(pid!=0){
		HANDLE process = OpenProcess(MAXIMUM_ALLOWED, FALSE, pid);
		if(process == NULL) {
			printf("[Error] the specified process couldn't be found. Code: %d\n",GetLastError());
			sprintf(cmd,"Error: Invalid Pid %d",pid);
			logger(ini,"injector",cmd,strlen(cmd));
			return 1;
		}
	}
	if(ini->debuglevel>3){
		sprintf(cmd,"sinjector.exe called...",pid);
		logger(ini,"injector",cmd,strlen(cmd));
		sprintf(cmd,"Ini: %s",iniFilePath);
		logger(ini,"injector",cmd,strlen(cmd));
		sprintf(cmd,"DLL: %s",dllToInject);
		logger(ini,"injector",cmd,strlen(cmd));
	}

	//dump ini options
	if(ini && ini->debuglevel>0){
		printf("[ini] dll=%s\n",*ini->dll?ini->dll:"Error!!!");
		if(ini->monitor) printf("[ini] monitor=%s\n",*ini->monitor?ini->monitor:"none (DISABLED)");
		if(ini->logfile) printf("[ini] logfile=%s\n",*ini->logfile?ini->logfile:"none (DISABLED)");
		if(ini->iatfile) printf("[ini] iatfile=%s\n",*ini->iatfile?ini->iatfile:"none (DISABLED)");
		if(ini->backup)  printf("[ini] backup=%s\n",*ini->backup?ini->backup:"none (DISABLED)");
		printf("[ini] debuglevel=%d\n",ini->debuglevel);
		printf("[ini] reinject=%d (%s)\n",ini->reinject,ini->reinject?"ENABLED":"DISABLED");
		if(ini->reinject_blacklist) printf("[ini] reinject_blacklist=%s\n",*ini->reinject_blacklist?ini->reinject_blacklist:"none (DISABLED)");
	}else{
		options.loadINI=FALSE;
	}

	if(options.cmdline!=NULL){
		spwanAndHook(dllToInject,&options);
	}else{
		injecta(pid,dllToInject,&options);
	}
	//printf("Press [intro] to exit...\n");
	//getchar();
	return 0;
	/*
	//Classic DLL Injection
	//Get process handle passing in the process ID.
	HANDLE process = OpenProcess(MAXIMUM_ALLOWED, FALSE, pid);
	if(process == NULL) {
		printf("Error: the specified process couldn't be found\n");
		printf("PID: %d Last error: %d\n",pid,GetLastError());
		return FALSE;
	}
	 
	//Get address of the LoadLibrary function.
	LPVOID addrLoadLib = (LPVOID)GetProcAddress(GetModuleHandle("kernel32.dll"), "LoadLibraryA");
	if(addrLoadLib == NULL) {
	printf("Error: the LoadLibraryA function was not found inside kernel32.dll library\n");
	}
	 
	//Allocate new memory region inside the process's address space.
	LPVOID arg = (LPVOID)VirtualAllocEx(process, NULL, strlen(buffer), MEM_RESERVE | MEM_COMMIT, PAGE_READWRITE);
	if(arg == NULL) {
	printf("Error: the memory could not be allocated inside the chosen process\n");
	}
	 
	//Write the argument to LoadLibraryA to the process's newly allocated memory region.
	int n = WriteProcessMemory(process, arg, buffer, strlen(buffer), NULL);
	if(n == 0) {
	printf("Error: there was no bytes written to the process's address space\n");
	}
	 
	//Inject our DLL into the process's address space.
	printf("Waiting for process (if it's suspended...)\n");   
	WaitForInputIdle(process,INFINITE);

	HANDLE threadID = CreateRemoteThread(process, NULL, 0, (LPTHREAD_START_ROUTINE)addrLoadLib, arg, 0, NULL);
	if(threadID == NULL) {
	printf("Error: the remote thread could not be created\n");
	}
	else {
	printf("Success: the remote thread was successfully created\n");
	}
	 
	//Close the handle to the process, becuase we've already injected the DLL.
	CloseHandle(process);
	*/	 
	return 0;
}
void spwanAndHook_(char *dlltoinject,opt *options){
	STARTUPINFO         sInfo;
	PROCESS_INFORMATION pInfo;

	printf("[Info] Launching process: %s\n",options->cmdline);
	ZeroMemory(&sInfo, sizeof(sInfo));
	sInfo.cb = sizeof(sInfo);
	ZeroMemory(&pInfo, sizeof(pInfo));

	if (CreateProcess(options->cmdline, NULL, NULL, NULL, FALSE, DEBUG_ONLY_THIS_PROCESS/*CREATE_SUSPENDED*/, NULL, NULL, &sInfo, &pInfo))
	{
		char cmd[512];
		printf("[info] New pid: %d\n",pInfo.dwProcessId);
		sprintf(cmd,"EXECUTING \"%s\" HOOKING PID %d",options->cmdline,pInfo.dwProcessId);
		logger(options->ini,"injector",cmd,strlen(cmd));

		DEBUG_EVENT             debugEvent;

		do{
//			printf(">> %x\n",debugEvent.dwDebugEventCode);
		    if(!WaitForDebugEvent(&debugEvent, 1000)) break;
		    if(debugEvent.dwDebugEventCode == LOAD_DLL_DEBUG_EVENT)
		    {
//				SuspendThread(debugEvent.u.CreateProcessInfo.hThread);
//				printf("Injecting!\n");
				injecta(pInfo.dwProcessId,dlltoinject,options);
				Sleep(1000);
//				ResumeThread(debugEvent.u.CreateProcessInfo.hThread);
				DebugSetProcessKillOnExit(FALSE);
		        DebugActiveProcessStop(debugEvent.dwProcessId);
				break;
		    }
		    ContinueDebugEvent(debugEvent.dwProcessId, debugEvent.dwThreadId, DBG_CONTINUE);
		} while(1);

		/*
		if(WaitForInputIdle((void*)pInfo.hProcess,5)==WAIT_FAILED) printf("[info] Wait failed, console app? :p");
		if(options->waitKeyPress){
			printf("Press [intro] to resume process..\n");
			getchar();
		}*/
//		injecta(pInfo.dwProcessId,dlltoinject,options);
//		ResumeThread((void*)pInfo.hThread);
//		WaitForInputIdle((void*)pInfo.dwProcessId,INFINITE);
//		Sleep(100);
	    CloseHandle(pInfo.hThread);
	    CloseHandle(pInfo.hProcess);
	}else{
		printf("[Error] Unable to create the process (path not found?)\n");
	}
}
