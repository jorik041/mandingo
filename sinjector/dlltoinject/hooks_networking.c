#include "hooks_networking.h"

HINTERNET MyHttpOpenRequest(HINTERNET hConnect,LPCTSTR lpszVerb,LPCTSTR lpszObjectName,LPCTSTR lpszVersion,LPCTSTR lpszReferer,LPCTSTR *lplpszAcceptTypes,DWORD dwFlags,DWORD_PTR dwContext){
	RestoreHook("HttpOpenRequestA");
	HINTERNET r=HttpOpenRequestA( hConnect, lpszVerb, lpszObjectName, lpszVersion, lpszReferer, lplpszAcceptTypes, dwFlags, dwContext);
	DWORD lastError=GetLastError();
	HookAgain("HttpOpenRequestA");
	char temp[1024];
	snprintf(temp,sizeof(temp),"hConnect=%d \"%s\" \"%s\"",(int)hConnect,lpszVerb,lpszObjectName);
	logger(ini,"HttpOpenRequestA",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
HINTERNET MyHttpOpenRequestW(HINTERNET hConnect,LPCWSTR lpszVerb,LPCWSTR lpszObjectName,LPCWSTR lpszVersion,LPCWSTR lpszReferer,LPCWSTR *lplpszAcceptTypes,DWORD dwFlags,DWORD_PTR dwContext){
	RestoreHook("HttpOpenRequestW");
	HINTERNET r=HttpOpenRequestW( hConnect, lpszVerb, lpszObjectName, lpszVersion, lpszReferer, lplpszAcceptTypes, dwFlags, dwContext);
	DWORD lastError=GetLastError();
	HookAgain("HttpOpenRequestW");
	if(!IsBadReadPtr(lpszVerb,1) && !IsBadReadPtr(lpszObjectName,1)) {
		char temp[1024],verb[64],objectname[512];
		wcstombs ( verb, lpszVerb, sizeof(verb) );
		wcstombs ( objectname, lpszObjectName, sizeof(objectname) );
		snprintf(temp,sizeof(temp),"hConnect=%d \"%s\" \"%s\"",(int)hConnect,verb,objectname);
		logger(ini,"HttpOpenRequestW",(char*)temp,strlen(temp));
	}
	SetLastError(lastError);
	return r;
}
HINTERNET MyInternetConnectW(HINTERNET hInternet,LPCWSTR lpszServerName,INTERNET_PORT nServerPort,LPCWSTR lpszUsername,LPCWSTR lpszPassword,DWORD dwService,DWORD dwFlags,DWORD_PTR dwContext){
	RestoreHook("InternetConnectW");
	HINTERNET r=InternetConnectW( hInternet, lpszServerName, nServerPort, lpszUsername, lpszPassword, dwService, dwFlags, dwContext);
	DWORD lastError=GetLastError();
	HookAgain("InternetConnectW");
	if(!IsBadReadPtr(lpszServerName,1)) {
		char temp[1024],servername[64];
		wcstombs ( servername, lpszServerName, sizeof(servername) );
		snprintf(temp,sizeof(temp),"hInternet=%d \"%s\" \"%d\"",(int)r,servername,nServerPort);
		logger(ini,"InternetConnectW",(char*)temp,strlen(temp));
	}
	SetLastError(lastError);
	return r;
}
HINTERNET MyInternetConnect(HINTERNET hInternet,LPCTSTR lpszServerName,INTERNET_PORT nServerPort,LPCTSTR lpszUsername,LPCTSTR lpszPassword,DWORD dwService,DWORD dwFlags,DWORD_PTR dwContext){
	RestoreHook("InternetConnectA");
	HINTERNET r=InternetConnectA( hInternet, lpszServerName, nServerPort, lpszUsername, lpszPassword, dwService, dwFlags, dwContext);
	DWORD lastError=GetLastError();
	HookAgain("InternetConnectA");
	char temp[1024];
	snprintf(temp,sizeof(temp),"hInternet=%d \"%s\" \"%d\"",(int)r,lpszServerName,nServerPort);
	logger(ini,"InternetConnectA",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
int Myconnect(SOCKET s,const struct sockaddr_in *name,int namelen){
	RestoreHook("connect");
	int r=connect(s,(SOCKADDR*)name,namelen);
	DWORD lastError=GetLastError();
	HookAgain("connect");
	char temp[1024];
	snprintf(temp,sizeof(temp),"\"%s\" \"%d\"",inet_ntoa(name->sin_addr),ntohs(name->sin_port));
	logger(ini,"connect",(char*)temp,strlen(temp));
	SetLastError(lastError);
	return r;
}
int WSAAPI MyGetAddrInfoW(PCWSTR pNodeName,PCWSTR pServiceName,const ADDRINFOW *pHints,PADDRINFOW *ppResult){
	RestoreHook("GetAddrInfoW");
	int r=GetAddrInfoW( pNodeName, pServiceName,pHints,ppResult);
	DWORD lastError=GetLastError();
	HookAgain("GetAddrInfoW");
	if(!IsBadReadPtr(pNodeName,1) && !IsBadReadPtr(pServiceName,1)) {
		char temp[1024],nodename[512],servicename[64];
		wcstombs ( nodename, pNodeName, sizeof(nodename) );
		wcstombs ( servicename, pServiceName, sizeof(servicename) );
		snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",nodename,servicename);
		logger(ini,"GetAddrInfoW",(char*)temp,strlen(temp));
	}
	SetLastError(lastError);
	return r;
}

int WSAAPI Mygetaddrinfo(PCSTR pNodeName,PCSTR pServiceName,const ADDRINFOA *pHints,PADDRINFOA *ppResult){
	RestoreHook("getaddrinfo");
	int r=getaddrinfo( pNodeName, pServiceName,pHints,ppResult);
	DWORD lastError=GetLastError();
	HookAgain("getaddrinfo");
	if(!IsBadReadPtr(pNodeName,1) && !IsBadReadPtr(pServiceName,1)) {
		char temp[1024];
		snprintf(temp,sizeof(temp),"\"%s\" \"%s\"",pNodeName,pServiceName);
		logger(ini,"getaddrinfo",(char*)temp,strlen(temp));
	}
	SetLastError(lastError);
	return r;
}
unsigned long Myinet_addr(const char *cp){
	RestoreHook("inet_addr");
	unsigned long r=inet_addr(cp);
	DWORD lastError=GetLastError();
	HookAgain("inet_addr");
	if(!IsBadReadPtr(cp,1)) {
		char temp[1024];
		snprintf(temp,sizeof(temp),"\"%s\" \"%x\"",cp,r);
		logger(ini,"inet_addr",temp,strlen(temp));
	}
	SetLastError(lastError);
	return r;
}
