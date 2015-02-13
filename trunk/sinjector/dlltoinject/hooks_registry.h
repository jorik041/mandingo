#include "hooker.h"

LONG WINAPI MyRegCreateKeyEx(HKEY hKey,LPCTSTR lpSubKey,DWORD Reserved,LPTSTR lpClass,DWORD dwOptions,REGSAM samDesired,LPSECURITY_ATTRIBUTES lpSecurityAttributes,PHKEY phkResult,LPDWORD lpdwDisposition);
LONG WINAPI MyRegCreateKeyExW(HKEY hKey,LPCWSTR lpSubKey,DWORD Reserved,LPWSTR lpClass,DWORD dwOptions,REGSAM samDesired,LPSECURITY_ATTRIBUTES lpSecurityAttributes,PHKEY phkResult,LPDWORD lpdwDisposition);
LONG WINAPI MyRegCreateKey(HKEY hKey,LPCTSTR lpSubKey,PHKEY phkResult);
LONG WINAPI MyRegCreateKeyW(HKEY hKey,LPCWSTR lpSubKey,PHKEY phkResult);
LONG WINAPI MyRegConnectRegistry(LPCTSTR lpMachineName,HKEY hKey,PHKEY phkResult);
LONG WINAPI MyRegConnectRegistryW(LPCWSTR lpMachineName,HKEY hKey,PHKEY phkResult);
LONG WINAPI MyRegOpenKeyW(HKEY hKey,LPCWSTR lpSubKey,PHKEY phkResult);
LONG WINAPI MyRegOpenKey(HKEY hKey,LPCTSTR lpSubKey,PHKEY phkResult);
LONG WINAPI MyRegOpenKeyExW(HKEY hKey,LPCWSTR lpSubKey,DWORD ulOptions,REGSAM samDesired,PHKEY phkResult);
LONG WINAPI MyRegOpenKeyExA(HKEY hKey,LPCTSTR lpSubKey,DWORD ulOptions,REGSAM samDesired,PHKEY phkResult);
LONG WINAPI MyRegSetValueEx(HKEY hKey,LPCTSTR lpValueName,DWORD Reserved,DWORD dwType,const BYTE *lpData,DWORD cbData);
LONG WINAPI MyRegSetValueExW(HKEY hKey,LPCWSTR lpValueName,DWORD Reserved,DWORD dwType,const BYTE *lpData,DWORD cbData);
LONG WINAPI MyRegQueryValueExA(HKEY hKey,LPTSTR lpValueName,LPDWORD lpReserved,LPDWORD lpType,LPBYTE lpData,LPDWORD lpcbData);
LONG WINAPI MyRegQueryValueExW(HKEY hKey,LPCWSTR lpValueName,LPDWORD lpReserved,LPDWORD lpType,LPBYTE lpData,LPDWORD lpcbData);
char *regkeytostring(HKEY hKey,REGSAM samDesired);
char *regHandleToString(HKEY hKey,DWORD type);
