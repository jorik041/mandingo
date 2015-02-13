#include "hooks_fileops.h"

BOOL WINAPI MyCopyFileEx(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName,LPPROGRESS_ROUTINE lpProgressRoutine,LPVOID lpData,LPBOOL pbCancel,DWORD dwCopyFlags){
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileExW");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	BOOL r=CopyFileExA( lpExistingFileName, lpNewFileName, lpProgressRoutine, lpData, pbCancel, dwCopyFlags);
	DWORD lastError=GetLastError();
	HookAgain("CopyFileExW");
	HookAgain("CopyFileExA");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",lpExistingFileName,lpNewFileName);
	logger(ini,"CopyFileExA",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyCopyFileExW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName,LPPROGRESS_ROUTINE lpProgressRoutine,LPVOID lpData,LPBOOL pbCancel,DWORD dwCopyFlags){
	RestoreHook("CopyFileExW");
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	BOOL r=CopyFileExW( lpExistingFileName, lpNewFileName, lpProgressRoutine, lpData, pbCancel, dwCopyFlags);
	DWORD lastError=GetLastError();
	HookAgain("CopyFileExA");
	HookAgain("CopyFileExW");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	char existingfilename[MAX_PATH],newfilename[MAX_PATH];
	memset(existingfilename,0,sizeof(existingfilename));
	memset(newfilename,0,sizeof(newfilename));
	if(!IsBadReadPtr(lpExistingFileName,1)) wcstombs ( existingfilename, lpExistingFileName, sizeof(existingfilename) );
	if(!IsBadReadPtr(lpNewFileName,1)) wcstombs ( newfilename, lpNewFileName, sizeof(newfilename) );
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",existingfilename,newfilename);
	logger(ini,"CopyFileExW",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyCopyFile(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName,BOOL bFailIfExists){
	RestoreHook("CopyFileExW");
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	BOOL r=CopyFileA( lpExistingFileName, lpNewFileName, bFailIfExists);
	DWORD lastError=GetLastError();
	HookAgain("CopyFileExA");
	HookAgain("CopyFileExW");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",lpExistingFileName,lpNewFileName);
	logger(ini,"CopyFileA",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyCopyFileW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName,BOOL bFailIfExists){
	RestoreHook("CopyFileExW");
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	BOOL r=CopyFileW( lpExistingFileName, lpNewFileName, bFailIfExists);
	DWORD lastError=GetLastError();
	HookAgain("CopyFileExA");
	HookAgain("CopyFileExW");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	char existingfilename[MAX_PATH],newfilename[MAX_PATH];
	*existingfilename=0;
	*newfilename=0;
	if(!IsBadReadPtr(lpExistingFileName,1)) wcstombs ( existingfilename, lpExistingFileName, sizeof(existingfilename) );
	if(!IsBadReadPtr(lpNewFileName,1)) wcstombs ( newfilename, lpNewFileName, sizeof(newfilename) );
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",existingfilename,newfilename);
	logger(ini,"CopyFileW",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyMoveFile(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName){
	RestoreHook("MoveFileA");
	BOOL r=MoveFileA(lpExistingFileName, lpNewFileName);
	DWORD lastError=GetLastError();
	HookAgain("MoveFileA");
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",lpExistingFileName,lpNewFileName);
	logger(ini,"MoveFileA",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyMoveFileW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName){
	RestoreHook("MoveFileW");
	BOOL r=MoveFileW(lpExistingFileName, lpNewFileName);
	DWORD lastError=GetLastError();
	HookAgain("MoveFileW");
	char existingfilename[MAX_PATH],newfilename[MAX_PATH];
	*existingfilename=0;
	*newfilename=0;
	if(!IsBadReadPtr(lpExistingFileName,1)) wcstombs ( existingfilename, lpExistingFileName, sizeof(existingfilename) );
	if(!IsBadReadPtr(lpNewFileName,1)) wcstombs ( newfilename, lpNewFileName, sizeof(newfilename) );
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",existingfilename,newfilename);
	logger(ini,"MoveFileW",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyMoveFileEx(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName,DWORD dwFlags){
	RestoreHook("MoveFileExA");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileExW");
	BOOL r=MoveFileExA( lpExistingFileName, lpNewFileName, dwFlags);
	DWORD lastError=GetLastError();
	HookAgain("MoveFileExA");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	HookAgain("CopyFileExA");
	HookAgain("CopyFileExW");
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",lpExistingFileName,lpNewFileName);
	logger(ini,"MoveFileExA",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyMoveFileExW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName,DWORD dwFlags){
	RestoreHook("MoveFileExW");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileExW");
	BOOL r=MoveFileExW( lpExistingFileName, lpNewFileName, dwFlags);
	DWORD lastError=GetLastError();
	HookAgain("MoveFileExW");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	HookAgain("CopyFileExA");
	HookAgain("CopyFileExW");
	char existingfilename[MAX_PATH],newfilename[MAX_PATH];
	*existingfilename=0;
	*newfilename=0;
	if(!IsBadReadPtr(lpExistingFileName,1)) wcstombs ( existingfilename, lpExistingFileName, sizeof(existingfilename) );
	if(!IsBadReadPtr(lpNewFileName,1)) wcstombs ( newfilename, lpNewFileName, sizeof(newfilename) );
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",existingfilename,newfilename);
	logger(ini,"MoveFileExW",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
BOOL WINAPI MyDeleteFileW(LPCWSTR lpFileName){
	char fname[MAX_PATH],dest[MAX_PATH];
	RestoreHook("DeleteFileW");
	RestoreHook("DeleteFileA");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileExW");
	wcstombs ( fname, lpFileName, sizeof(fname) );
	if(ini->backup){
		_mkdir(ini->backup);
		snprintf(dest,sizeof(dest),"%s\\%s",ini->backup,filename(fname));
		CopyFile(fname,dest,FALSE);
	}
	BOOL r=DeleteFileW(lpFileName);
	DWORD lastError=GetLastError();
	logger(ini,"DeleteFileW",(char*)fname,strlen(fname));
	HookAgain("DeleteFileA");
	HookAgain("DeleteFileW");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	HookAgain("CopyFileExA");
	HookAgain("CopyFileExW");
	SetLastError(lastError);
	return r;
}

BOOL WINAPI MyDeleteFile(LPTSTR lpFileName){
	char dest[MAX_PATH];
	RestoreHook("DeleteFileA");
	RestoreHook("DeleteFileW");
	RestoreHook("CopyFileA");
	RestoreHook("CopyFileW");
	RestoreHook("CopyFileExA");
	RestoreHook("CopyFileExW");
	if(ini->backup){
		_mkdir(ini->backup);
		snprintf(dest,sizeof(dest),"%s\\%s",ini->backup,filename(lpFileName));
		CopyFile(lpFileName,dest,FALSE);
	}
	BOOL r=DeleteFileA(lpFileName);
	DWORD lastError=GetLastError();
	if(!IsBadReadPtr(lpFileName,1)) logger(ini,"DeleteFileA",lpFileName,strlen(lpFileName));
	HookAgain("DeleteFileW");
	HookAgain("DeleteFileA");
	HookAgain("CopyFileA");
	HookAgain("CopyFileW");
	HookAgain("CopyFileExA");
	HookAgain("CopyFileExW");
	SetLastError(lastError);
	return r;
}

HANDLE WINAPI MyCreateFile(LPCTSTR lpFileName,
                          DWORD dwDesiredAccess,
                          DWORD dwShareMode,
                          LPSECURITY_ATTRIBUTES lpSecurityAttributes,
                          DWORD dwCreationDisposition,
                          DWORD dwFlagsAndAttributes,
                          HANDLE hTemplateFile){
	char cmd[1024],access[1024];


	*access=0;
	if(dwDesiredAccess&GENERIC_ALL) 			strncat(access,"gALL ",sizeof(access));
	if(dwDesiredAccess&GENERIC_READ) 			strncat(access,"gREA ",sizeof(access));
	if(dwDesiredAccess&GENERIC_WRITE) 			strncat(access,"gWRI ",sizeof(access));
	if(dwDesiredAccess&GENERIC_EXECUTE)			strncat(access,"gEXE ",sizeof(access));
	if(dwDesiredAccess&FILE_WRITE_ATTRIBUTES)	strncat(access,"fWattr ",sizeof(access));
	if(dwDesiredAccess&FILE_READ_ATTRIBUTES)	strncat(access,"fRattr ",sizeof(access));
	if(dwDesiredAccess&FILE_READ_DATA)			strncat(access,"fRdata ",sizeof(access));

	if(dwCreationDisposition&OPEN_EXISTING)		strncat(access,"OE ",sizeof(access));

	char *fullPath=getfile_fullpath((char*)lpFileName);
	
	sprintf(cmd,"CreateFileA (%s0x%x)",access,dwDesiredAccess);
	RestoreHook("CreateFileA");
	HANDLE r=CreateFileA(lpFileName,dwDesiredAccess,dwShareMode,lpSecurityAttributes,dwCreationDisposition,dwFlagsAndAttributes,hTemplateFile);
	DWORD lastError=GetLastError();
	HookAgain("CreateFileA");
	logger(ini,cmd,fullPath,strlen(fullPath));
	SetLastError(lastError);
	return r;
}

HANDLE WINAPI MyCreateFileW(LPCWSTR lpFileName,
                          DWORD dwDesiredAccess,
                          DWORD dwShareMode,
                          LPSECURITY_ATTRIBUTES lpSecurityAttributes,
                          DWORD dwCreationDisposition,
                          DWORD dwFlagsAndAttributes,
                          HANDLE hTemplateFile){
	char fname[1024],cmd[1024],access[1024];


	*access=0;
	if(dwDesiredAccess&GENERIC_ALL) 			strncat(access,"gALL ",sizeof(access));
	if(dwDesiredAccess&GENERIC_READ) 			strncat(access,"gREA ",sizeof(access));
	if(dwDesiredAccess&GENERIC_WRITE) 			strncat(access,"gWRI ",sizeof(access));
	if(dwDesiredAccess&GENERIC_EXECUTE)			strncat(access,"gEXE ",sizeof(access));
	if(dwDesiredAccess&FILE_WRITE_ATTRIBUTES)	strncat(access,"fWattr ",sizeof(access));
	if(dwDesiredAccess&FILE_READ_ATTRIBUTES)	strncat(access,"fRattr ",sizeof(access));
	if(dwDesiredAccess&FILE_READ_DATA)			strncat(access,"fRdata ",sizeof(access));

	if(dwCreationDisposition&OPEN_EXISTING)		strncat(access,"OE ",sizeof(access));
	
	wcstombs ( fname, lpFileName, sizeof(fname) );
	
	char *fullPath=getfile_fullpath(fname);
	
	sprintf(cmd,"CreateFileW (%s0x%x)",access,dwDesiredAccess);
	char msg[1024];
	strncpy(msg,"(you can dump the process now with external tools)\n\nFileName: ",sizeof(msg));
	strncat(msg,fullPath,sizeof(msg));
	strncat(msg,"\n\nAllow this file action?\n\n",sizeof(msg));
	strncat(msg,cmd,sizeof(msg));
	BOOL allow=TRUE;
	if(ini && ini->monitor && StrStrI(fullPath,ini->monitor)){
		switch(MessageBox(NULL,msg,"File operation detected",MB_YESNO|MB_ICONASTERISK)){
			case IDYES:
				break;
			case IDNO:
				allow=FALSE;
				break;
		}
	}
	
	if(!allow) return 0;
	RestoreHook("CreateFileW");  
	HANDLE r=CreateFileW(lpFileName,dwDesiredAccess,dwShareMode,lpSecurityAttributes,dwCreationDisposition,dwFlagsAndAttributes,hTemplateFile);
	DWORD lastError=GetLastError();
	if(strcmp(ini->logfile,fullPath)) logger(ini,cmd,(char*)fullPath,strlen(fullPath));
	//if(STRCMP(ini->logfile,fullPath)) logger(ini,cmd,(char*)fullPath,strlen(fullPath));
	HookAgain("CreateFileW");
	SetLastError(lastError);
	return r;
}
