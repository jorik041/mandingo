#include <winsock2.h>
#include <ws2tcpip.h>
#include <windows.h>
#include <Wininet.h>
#include "hooker.h"

HINTERNET MyHttpOpenRequest(HINTERNET hConnect,LPCTSTR lpszVerb,LPCTSTR lpszObjectName,LPCTSTR lpszVersion,LPCTSTR lpszReferer,LPCTSTR *lplpszAcceptTypes,DWORD dwFlags,DWORD_PTR dwContext);
HINTERNET MyHttpOpenRequestW(HINTERNET hConnect,LPCWSTR lpszVerb,LPCWSTR lpszObjectName,LPCWSTR lpszVersion,LPCWSTR lpszReferer,LPCWSTR *lplpszAcceptTypes,DWORD dwFlags,DWORD_PTR dwContext);
HINTERNET MyInternetConnectW(HINTERNET hInternet,LPCWSTR lpszServerName,INTERNET_PORT nServerPort,LPCWSTR lpszUsername,LPCWSTR lpszPassword,DWORD dwService,DWORD dwFlags,DWORD_PTR dwContext);
HINTERNET MyInternetConnect(HINTERNET hInternet,LPCTSTR lpszServerName,INTERNET_PORT nServerPort,LPCTSTR lpszUsername,LPCTSTR lpszPassword,DWORD dwService,DWORD dwFlags,DWORD_PTR dwContext);
int Myconnect(SOCKET s,const struct sockaddr_in *name,int namelen);
int WSAAPI MyGetAddrInfoW(PCWSTR pNodeName,PCWSTR pServiceName,const ADDRINFOW *pHints,PADDRINFOW *ppResult);
int WSAAPI Mygetaddrinfo(PCSTR pNodeName,PCSTR pServiceName,const ADDRINFOA *pHints,PADDRINFOA *ppResult);
unsigned long Myinet_addr(const char *cp);
