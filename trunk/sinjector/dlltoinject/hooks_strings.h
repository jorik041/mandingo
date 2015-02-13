#include "hooker.h"

int WINAPI Mylstrcmpi(LPCTSTR lpString1,LPCTSTR lpString2);
int WINAPI MylstrcmpiW(LPCWSTR lpString1,LPCWSTR lpString2);
int WINAPI Mylstrcmp(LPCTSTR lpString1,LPCTSTR lpString2);
int WINAPI MylstrcmpW(LPCWSTR lpString1,LPCWSTR lpString2);
