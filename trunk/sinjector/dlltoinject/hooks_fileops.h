#include "hooker.h"

/*
Notes:
	- if "copyfile" is enabled, "create file is not shown" (but it's interesting seeing the operation)
*/

BOOL WINAPI MyMoveFileEx(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName,DWORD dwFlags);
BOOL WINAPI MyMoveFileExW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName,DWORD dwFlags);
BOOL WINAPI MyDeleteFileW(LPCWSTR lpFileName);
BOOL WINAPI MyDeleteFile(LPTSTR lpFileName);
HANDLE WINAPI MyCreateFile(LPCTSTR lpFileName,
                          DWORD dwDesiredAccess,
                          DWORD dwShareMode,
                          LPSECURITY_ATTRIBUTES lpSecurityAttributes,
                          DWORD dwCreationDisposition,
                          DWORD dwFlagsAndAttributes,
                          HANDLE hTemplateFile);
HANDLE WINAPI MyCreateFileW(LPCWSTR lpFileName,
                          DWORD dwDesiredAccess,
                          DWORD dwShareMode,
                          LPSECURITY_ATTRIBUTES lpSecurityAttributes,
                          DWORD dwCreationDisposition,
                          DWORD dwFlagsAndAttributes,
                          HANDLE hTemplateFile);
BOOL WINAPI MyCopyFile(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName,BOOL bFailIfExists);
BOOL WINAPI MyCopyFileW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName,BOOL bFailIfExists);
BOOL WINAPI MyCopyFileEx(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName,LPPROGRESS_ROUTINE lpProgressRoutine,LPVOID lpData,LPBOOL pbCancel,DWORD dwCopyFlags);
BOOL WINAPI MyCopyFileExW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName,LPPROGRESS_ROUTINE lpProgressRoutine,LPVOID lpData,LPBOOL pbCancel,DWORD dwCopyFlags);
BOOL WINAPI MyMoveFile(LPCTSTR lpExistingFileName,LPCTSTR lpNewFileName);
BOOL WINAPI MyMoveFileW(LPCWSTR lpExistingFileName,LPCWSTR lpNewFileName);
