#include "hooks_registry.h"

LONG WINAPI MyRegCreateKeyEx(HKEY hKey,LPCTSTR lpSubKey,DWORD Reserved,LPTSTR lpClass,DWORD dwOptions,REGSAM samDesired,LPSECURITY_ATTRIBUTES lpSecurityAttributes,PHKEY phkResult,LPDWORD lpdwDisposition){
	char key[1024],final[1024];
	RestoreHook("RegCreateKeyExA");
	LONG r=RegCreateKeyExA( hKey, lpSubKey, Reserved, lpClass, dwOptions, samDesired, lpSecurityAttributes, phkResult, lpdwDisposition);
	DWORD lastError=GetLastError();
	HookAgain("RegCreateKeyExA");
	strcpy(key,regkeytostring(hKey,samDesired));
	if(!IsBadReadPtr(lpSubKey,1)) strncat(key,lpSubKey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegCreateKeyExA",final,strlen(final));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegCreateKeyExW(HKEY hKey,LPCWSTR lpSubKey,DWORD Reserved,LPWSTR lpClass,DWORD dwOptions,REGSAM samDesired,LPSECURITY_ATTRIBUTES lpSecurityAttributes,PHKEY phkResult,LPDWORD lpdwDisposition){
	char subkey[1024],key[1024],final[1024];
	RestoreHook("RegCreateKeyExW");
	LONG r=RegCreateKeyExW( hKey, lpSubKey, Reserved, lpClass, dwOptions, samDesired, lpSecurityAttributes, phkResult, lpdwDisposition);
	DWORD lastError=GetLastError();
	HookAgain("RegCreateKeyExW");
	*subkey=0;
	if(!IsBadReadPtr(lpSubKey,1)) wcstombs ( subkey, lpSubKey, sizeof(subkey));
	strcpy(key,regkeytostring(hKey,samDesired));
	if(*subkey) strncat(key,subkey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegCreateKeyExW",final,strlen(final));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegCreateKey(HKEY hKey,LPCTSTR lpSubKey,PHKEY phkResult){
	char key[1024],final[1024];
	RestoreHook("RegCreateKeyA");
	LONG r=RegCreateKeyA( hKey, lpSubKey, phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegCreateKeyA");
	strcpy(key,regkeytostring(hKey,0));
	if(!IsBadReadPtr(lpSubKey,1)) strncat(key,lpSubKey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegCreateKeyA",final,strlen(final));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegCreateKeyW(HKEY hKey,LPCWSTR lpSubKey,PHKEY phkResult){
	char subkey[1024],key[1024],final[1024];
	RestoreHook("RegCreateKeyW");
	LONG r=RegCreateKeyW( hKey, lpSubKey, phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegCreateKeyW");
	*subkey=0;
	if(!IsBadReadPtr(lpSubKey,1)) wcstombs ( subkey, lpSubKey, sizeof(subkey));
	strcpy(key,regkeytostring(hKey,0));
	strncat(key,subkey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegCreateKeyW",final,strlen(final));
	SetLastError(lastError);
	return r;
}

LONG WINAPI MyRegConnectRegistry(LPCTSTR lpMachineName,HKEY hKey,PHKEY phkResult){
	RestoreHook("RegConnectRegistryA");
	LONG r=RegConnectRegistryA( lpMachineName, hKey, phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegConnectRegistryA");
	logger(ini,"RegConnectRegistryA",NULL,0);
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegConnectRegistryW(LPCWSTR lpMachineName,HKEY hKey,PHKEY phkResult){
	RestoreHook("RegConnectRegistryW");
	LONG r=RegConnectRegistryW( lpMachineName, hKey, phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegConnectRegistryW");
	logger(ini,"RegConnectRegistryW",NULL,0);
	SetLastError(lastError);
	return r;
}

LONG WINAPI MyRegOpenKeyExW(HKEY hKey,LPCWSTR lpSubKey,DWORD ulOptions,REGSAM samDesired,PHKEY phkResult){
	char subkey[512],key[512],final[1024];

	RestoreHook("RegOpenKeyExW");
    BOOL r=RegOpenKeyExW(hKey,lpSubKey,ulOptions,samDesired,phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegOpenKeyExW");
	*subkey=0;
	if(!IsBadReadPtr(lpSubKey,1)) wcstombs ( subkey, lpSubKey, sizeof(subkey));
	strcpy(key,regkeytostring(hKey,samDesired));
	if(*subkey) strncat(key,subkey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegOpenKeyExW",final,strlen(final));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegOpenKeyExA(HKEY hKey,LPCTSTR lpSubKey,DWORD ulOptions,REGSAM samDesired,PHKEY phkResult){
	char key[512],final[1024];

	RestoreHook("RegOpenKeyExA");
    BOOL r=RegOpenKeyExA(hKey,lpSubKey,ulOptions,samDesired,phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegOpenKeyExA");
	strcpy(key,regkeytostring(hKey,samDesired));
	if(!IsBadReadPtr(lpSubKey,1)) strncat(key,lpSubKey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegOpenKeyExA",final,strlen(final));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegOpenKeyW(HKEY hKey,LPCWSTR lpSubKey,PHKEY phkResult){
	char subkey[512],key[512],final[1024];

	RestoreHook("RegOpenKeyW");
    BOOL r=RegOpenKeyW(hKey,lpSubKey,phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegOpenKeyW");
	*subkey=0;
	if(!IsBadReadPtr(lpSubKey,1)) wcstombs ( subkey, lpSubKey, sizeof(subkey));
	strcpy(key,regkeytostring(hKey,0));
	strncat(key,subkey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegOpenKeyW",final,strlen(final));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegOpenKey(HKEY hKey,LPCTSTR lpSubKey,PHKEY phkResult){
	char key[512],final[1024];
	RestoreHook("RegOpenKeyA");
    BOOL r=RegOpenKeyA(hKey,lpSubKey,phkResult);
	DWORD lastError=GetLastError();
	HookAgain("RegOpenKeyA");
	strcpy(key,regkeytostring(hKey,0));
	if(!IsBadReadPtr(lpSubKey,1)) strncat(key,lpSubKey,sizeof(key));
	strncat(key,"\"",sizeof(key));
	snprintf(final,sizeof(final),"handle=0x%x %s",(int)*phkResult,key);
	logger(ini,"RegOpenKeyA",final,strlen(final));
	SetLastError(lastError);
	return r;
}

LONG WINAPI MyRegSetValueEx(HKEY hKey,LPCTSTR lpValueName,DWORD Reserved,DWORD dwType,const BYTE *lpData,DWORD cbData){
	char temp[1024];
	RestoreHook("RegSetValueExA");
	LONG r=RegSetValueExA(hKey, lpValueName, Reserved, dwType,lpData, cbData);
	DWORD lastError=GetLastError();
	HookAgain("RegSetValueExA");
	if(dwType==REG_DWORD){
		snprintf(temp,sizeof(temp),"%s\\%s\" \"%d\"",regHandleToString(hKey,dwType),lpValueName,*lpData);
	}else if(dwType==REG_BINARY){
		snprintf(temp,sizeof(temp),"%s\\%s\" \"",regHandleToString(hKey,dwType),lpValueName);
		int i;
		BOOL dumped=FALSE;
		for(i=0;i<cbData;i++) {
			char tmp[8];
			if(!IsBadReadPtr(lpData+i,1)) {
				snprintf(tmp,sizeof(tmp),"%.2x",lpData[i]);
				strncat(temp,tmp,sizeof(temp));
				dumped=TRUE;
			}
			if(i>32 && dumped){
				strncat(temp,"...",sizeof(temp));
				break;
			}
		}
		strncat(temp,"\"",sizeof(temp));
	}else{
		snprintf(temp,sizeof(temp),"%s\\%s\" \"%s\"",regHandleToString(hKey,dwType),lpValueName,lpData);
	}
	logger(ini,"RegSetValueExA",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegSetValueExW(HKEY hKey,LPCWSTR lpValueName,DWORD Reserved,DWORD dwType,const BYTE *lpData,DWORD cbData){
	char temp[1024],valuename[512],data[1024];
	RestoreHook("RegSetValueExW");
	LONG r=RegSetValueExW(hKey, lpValueName, Reserved, dwType,lpData, cbData);
	DWORD lastError=GetLastError();
	HookAgain("RegSetValueExW");
	*valuename=0;
	*data=0;
	if(!IsBadReadPtr(lpValueName,1)) wcstombs ( valuename, lpValueName, sizeof(valuename));	
	if(!IsBadReadPtr(lpData,1)) wcstombs ( data, (wchar_t*)lpData, sizeof(data));
	if(dwType==REG_DWORD){
		snprintf(temp,sizeof(temp),"%s\\%s\" \"%d\"",regHandleToString(hKey,dwType),valuename,*data);
	}else if(dwType==REG_BINARY){
		snprintf(temp,sizeof(temp),"%s\\%s\" \"",regHandleToString(hKey,dwType),valuename);
		int i;
		for(i=0;i<cbData;i++) {
			char tmp[8];
			if(i>=sizeof(data)) break;
			if(i>32){
				strncat(temp,"...",sizeof(temp));
				break;
			}
			snprintf(tmp,sizeof(tmp),"%.2x",data[i]);
			strncat(temp,tmp,sizeof(temp));
		}
		strncat(temp,"\"",sizeof(temp));
	}else{
		snprintf(temp,sizeof(temp),"%s\\%s\" \"%s\"",regHandleToString(hKey,dwType),valuename,data);
	}
	logger(ini,"RegSetValueExW",temp,strlen(temp));
	SetLastError(lastError);
	return r;
}

LONG WINAPI MyRegQueryValueExA(HKEY hKey,LPTSTR lpValueName,LPDWORD lpReserved,LPDWORD lpType,LPBYTE lpData,LPDWORD lpcbData){
	char final[1024];
	char *valuename=NULL;
	unsigned char *data=NULL;
	DWORD type=-1;
	memset(final,0,sizeof(final));
	RestoreHook("RegQueryValueExA");
    LONG r=RegQueryValueExA( hKey, lpValueName, lpReserved, lpType, lpData, lpcbData);
	DWORD lastError=GetLastError();
	HookAgain("RegQueryValueExA");
	if(!IsBadReadPtr(lpValueName,1)) valuename=lpValueName;
	if(!IsBadReadPtr(lpData,1) && *lpcbData>0) data=lpData;
	if(lpType!=NULL && !IsBadReadPtr(lpType,1)) type=*lpType;

	if(r!=ERROR_SUCCESS){
		snprintf(final,sizeof(final),"%s\\%s\"",regHandleToString(hKey,type),valuename);
	}else{
		if(type==REG_DWORD && !IsBadReadPtr(data,1)){
			snprintf(final,sizeof(final),"%s\\%s\" \"%d\"",regHandleToString(hKey,type),valuename,*data);
		}else if(type==REG_BINARY){
			snprintf(final,sizeof(final),"%s\\%s\" \"",regHandleToString(hKey,type),valuename);
			int i;
			BOOL dumped=FALSE;
			for(i=0;i<*lpcbData;i++) {
				char tmp[8];
				if(!IsBadReadPtr(data+i,1)) {
					snprintf(tmp,sizeof(tmp),"%.2x",data[i]);
					strncat(final,tmp,sizeof(final));
					dumped=TRUE;
				}
				if(i>32 && dumped){
					strncat(final,"...",sizeof(final));
					break;
				}
			}
			strncat(final,"\"",sizeof(final));
		}else{
			snprintf(final,sizeof(final),"%s\\%s\" \"%s\"",regHandleToString(hKey,type),valuename,data);
		}
	}
	logger(ini,"RegQueryValueExA",final,strlen(final));
	SetLastError(lastError);
	return r;
}
LONG WINAPI MyRegQueryValueExW(HKEY hKey,LPCWSTR lpValueName,LPDWORD lpReserved,LPDWORD lpType,LPBYTE lpData,LPDWORD lpcbData){
	char final[1024];
	char valuename[256],data[512];
	DWORD type=-1;
	memset(valuename,0,sizeof(valuename));
	memset(data,0,sizeof(data));
	RestoreHook("RegQueryValueExW");
    LONG r=RegQueryValueExW( hKey, lpValueName, lpReserved, lpType, lpData, lpcbData);
	DWORD lastError=GetLastError();
	HookAgain("RegQueryValueExW");
	if(!IsBadReadPtr(lpcbData,1)) {
		if(!IsBadReadPtr(lpValueName,1)) wcstombs ( valuename, lpValueName, sizeof(valuename));
		if(!IsBadReadPtr(lpData,1) && *lpcbData>0) wcstombs ( data, (wchar_t*)lpData, sizeof(data));//(int)*lpcbData);
		if(lpType!=NULL && !IsBadReadPtr(lpType,1)) type=*lpType;

		if(r!=ERROR_SUCCESS){
			snprintf(final,sizeof(final),"%s\\%s\"",regHandleToString(hKey,type),valuename);
		}else{
			if(type==REG_DWORD && !IsBadReadPtr(data,1)) 
				snprintf(final,sizeof(final),"%s\\%s\" \"%d\"",regHandleToString(hKey,type),valuename,*data);
			else if(type==REG_BINARY){
				snprintf(final,sizeof(final),"%s\\%s\" \"",regHandleToString(hKey,type),valuename);
				int i;
				for(i=0;i<*lpcbData;i++) {
					char tmp[8];
					if(i>=sizeof(data)) break;
					if(i>32){
						strncat(final,"...",sizeof(final));
						break;
					}
					if(IsBadReadPtr(data+i,1)) break;
					snprintf(tmp,sizeof(tmp),"%.2x",data[i]);
					strncat(final,tmp,sizeof(final));
				}
				strncat(final,"\"",sizeof(final));
			}else{
				snprintf(final,sizeof(final),"%s\\%s\" \"%s\"",regHandleToString(hKey,type),valuename,data);
			}
		}
		logger(ini,"RegQueryValueExW",final,strlen(final));
	}
	SetLastError(lastError);
	return r;
}

char *regHandleToString(HKEY hKey,DWORD type){
	static char key[512];
	*key=0;

	if(type==REG_BINARY) strncat(key,"REG_BINARY ",sizeof(key));
	if(type==REG_DWORD) strncat(key,"REG_DWORD ",sizeof(key));
	if(type==REG_DWORD_LITTLE_ENDIAN) strncat(key,"REG_DWORD_LITTLE_ENDIAN ",sizeof(key));
	if(type==REG_DWORD_BIG_ENDIAN) strncat(key,"REG_DWORD_BIG_ENDIAN ",sizeof(key));
	if(type==REG_EXPAND_SZ) strncat(key,"REG_EXPAND_SZ ",sizeof(key));
	if(type==REG_LINK) strncat(key,"REG_LINK ",sizeof(key));
	if(type==REG_MULTI_SZ) strncat(key,"REG_MULTI_SZ ",sizeof(key));
	if(type==REG_NONE) strncat(key,"REG_NONE ",sizeof(key));
	if(type==REG_QWORD) strncat(key,"REG_QWORD ",sizeof(key));
	if(type==REG_QWORD_LITTLE_ENDIAN) strncat(key,"REG_QWORD_LITTLE_ENDIAN ",sizeof(key));
	if(type==REG_SZ) strncat(key,"REG_SZ ",sizeof(key));
	if(type==-1) strncat(key,"NULL ",sizeof(key));
	if(!strlen(key)) strncat(key,"UNKNOWN ",sizeof(key));

	strncat(key,"\"",sizeof(key));
	if(hKey==HKEY_CLASSES_ROOT) strncat(key,"HKEY_CLASSES_ROOT",sizeof(key));
	else if(hKey==HKEY_CURRENT_CONFIG) strncat(key,"HKEY_CURRENT_CONFIG",sizeof(key));
	else if(hKey==HKEY_CURRENT_USER) strncat(key,"HKEY_CURRENT_USER",sizeof(key));
	else if(hKey==HKEY_LOCAL_MACHINE) strncat(key,"HKEY_LOCAL_MACHINE",sizeof(key));
	else if(hKey==HKEY_PERFORMANCE_DATA) strncat(key,"HKEY_PERFORMANCE_DATA",sizeof(key));
	else if(hKey==HKEY_PERFORMANCE_NLSTEXT) strncat(key,"HKEY_PERFORMANCE_NLSTEXT",sizeof(key));
	else if(hKey==HKEY_PERFORMANCE_TEXT) strncat(key,"HKEY_PERFORMANCE_TEXT",sizeof(key));
	else if(hKey==HKEY_USERS) strncat(key,"HKEY_USERS",sizeof(key));
	else {
		char tmp[32];
		snprintf(tmp,sizeof(tmp),"handle(0x%x)",(int)hKey);
		strncat(key,tmp,sizeof(key));
	}
	return key;
}

char *regkeytostring(HKEY hKey,REGSAM samDesired){
	static char key[512];
	*key=0;
	if(samDesired&KEY_ALL_ACCESS) 
		strncat(key,"ALL_ACCESS ",sizeof(key));
	else{
		if(samDesired&KEY_CREATE_LINK) strncat(key,"CREATE_LINK ",sizeof(key));
		else if(samDesired&KEY_CREATE_SUB_KEY) strncat(key,"CREATE_SUB_KEY ",sizeof(key));
		else if(samDesired&KEY_ENUMERATE_SUB_KEYS) strncat(key,"ENUMERATE_SUB_KEYS ",sizeof(key));
		else if(samDesired&KEY_EXECUTE) strncat(key,"EXECUTE ",sizeof(key));
		else if(samDesired&KEY_QUERY_VALUE) strncat(key,"QUERY_VALUE ",sizeof(key));
		else if(samDesired&KEY_READ) strncat(key,"READ ",sizeof(key));
		else if(samDesired&KEY_SET_VALUE) strncat(key,"SET_VALUE ",sizeof(key));
		else if(samDesired&KEY_WOW64_32KEY) strncat(key,"WOW64_32KEY ",sizeof(key));
		else if(samDesired&KEY_WOW64_64KEY) strncat(key,"WOW64_64KEY ",sizeof(key));
		else if(samDesired&KEY_WRITE) strncat(key,"WRITE ",sizeof(key));
		else if(samDesired&0x2000000) strncat(key,"MAXIMUM_ALLOWED ",sizeof(key));
		else snprintf(key,sizeof(key),"0x%x ",(int)samDesired);
	}

	strncat(key,"\"",sizeof(key));
	if(hKey==HKEY_CLASSES_ROOT) strncat(key,"HKCR\\",sizeof(key));
	else if(hKey==HKEY_CURRENT_CONFIG) strncat(key,"HKCC\\",sizeof(key));
	else if(hKey==HKEY_CURRENT_USER) strncat(key,"HKCU\\",sizeof(key));
	else if(hKey==HKEY_LOCAL_MACHINE) strncat(key,"HKLM\\",sizeof(key));
	else if(hKey==HKEY_USERS) strncat(key,"HKU\\",sizeof(key));
	else {
		char tmp[32];
		snprintf(tmp,sizeof(tmp),"handle(0x%x)\\",(int)hKey);
		strncat(key,tmp,sizeof(key));
	}
	return key;
}
