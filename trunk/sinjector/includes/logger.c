#include <stdio.h>
#include <process.h>
#include <string.h>
#include <windows.h>
#include "inifile.h"
#include "../dlltoinject_inline_standalone/inlinehook.h"

void logger(iniFile *ini,char *msg,const char *data,int len){
#ifndef SINJECTOR
	RestoreHook("CreateFileA");
#endif
	HANDLE hFile = CreateFileA(ini->logfile, // file to be opened
	FILE_APPEND_DATA,
	FILE_SHARE_WRITE, // share for writing
	NULL, // default security
	OPEN_ALWAYS,
	FILE_ATTRIBUTE_NORMAL |FILE_ATTRIBUTE_ARCHIVE | SECURITY_IMPERSONATION,
	// normal file archive and impersonate client
	NULL); // no attr. template
	DWORD dwWritten;
	char tmp[2048];
	snprintf(tmp,sizeof(tmp),"[%-6d] [%s] %s%s",_getpid(),msg,len>0?data:"",(!len || data[len]!='\r')?"\r\n":"");
	WriteFile(hFile, tmp, strlen(tmp), &dwWritten, NULL);  
	CloseHandle(hFile);
#ifndef SINJECTOR
	HookAgain("CreateFileA");
#endif
}
