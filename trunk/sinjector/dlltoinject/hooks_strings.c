#include "hooks_strings.h"

int WINAPI Mylstrcmpi(LPCTSTR lpString1,LPCTSTR lpString2){
	RestoreHook("lstrcmpiA");
	int r=lstrcmpiA(lpString1,lpString2);
	DWORD lastError=GetLastError();	
	if(!IsBadReadPtr(lpString1,1) && !IsBadReadPtr(lpString2,1)){
		char temp[1024];
		snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",lpString1,lpString2);
		logger(ini,"lstrcmpiA",(char*)temp,strlen(temp));
	}
	HookAgain("lstrcmpiA");
	SetLastError(lastError);
	return r;
}
int WINAPI MylstrcmpiW(LPCWSTR lpString1,LPCWSTR lpString2){
	RestoreHook("lstrcmpiW");
	int r=lstrcmpiW(lpString1,lpString2);
	DWORD lastError=GetLastError();
	HookAgain("lstrcmpiW");
	char temp[1024],string1[1024],string2[1024];
	*string1=0;
	*string2=0;
	if(!IsBadReadPtr(lpString1,1)) wcstombs ( string1, lpString1, sizeof(string1) );		
	if(!IsBadReadPtr(lpString2,1)) wcstombs ( string2, lpString2, sizeof(string2) );		
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",string1,string2);
	logger(ini,"lstrcmpiW",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
int WINAPI Mylstrcmp(LPCTSTR lpString1,LPCTSTR lpString2){
	RestoreHook("lstrcmpA");
	int r=lstrcmpiA(lpString1,lpString2);
	DWORD lastError=GetLastError();
	if(!IsBadReadPtr(lpString1,1) && !IsBadReadPtr(lpString2,1)){
		char temp[1024];
		snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",lpString1,lpString2);
		logger(ini,"lstrcmpA",(char*)temp,strlen(temp));
	}
	HookAgain("lstrcmpA");
	SetLastError(lastError);
	return r;
}
int WINAPI MylstrcmpW(LPCWSTR lpString1,LPCWSTR lpString2){
	RestoreHook("lstrcmpW");
	int r=lstrcmpW(lpString1,lpString2);
	DWORD lastError=GetLastError();
	HookAgain("lstrcmpW");
	char temp[1024],string1[1024],string2[1024];
	*string1=0;
	*string2=0;
	if(!IsBadReadPtr(lpString1,1)) wcstombs ( string1, lpString1, sizeof(string1) );		
	if(!IsBadReadPtr(lpString2,1)) wcstombs ( string2, lpString2, sizeof(string2) );		
	snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",string1,string2);
	logger(ini,"lstrcmpW",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
