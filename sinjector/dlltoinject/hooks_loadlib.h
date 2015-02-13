#include "hooker.h"

HMODULE WINAPI MyGetModuleHandle(LPCTSTR lpModuleName);
HMODULE WINAPI MyGetModuleHandleW(LPCWSTR lpModuleName);
FARPROC WINAPI MyGetProcAddress(HMODULE hModule,LPCSTR lpProcName);
HMODULE WINAPI MyLoadLibrary(LPCTSTR lpFileName);
HMODULE WINAPI MyLoadLibraryW(LPCWSTR lpFileName);
