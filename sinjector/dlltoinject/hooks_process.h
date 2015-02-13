#include "hooker.h"
/*
Notes:
* Detected with "CreateProcessW":
	- ShellExecute, ShellExecuteA, ShellExecuteW, ShellExecuteEx, ShellExecuteExA, ShellExecuteExW
* Detected with "CreteProcessInternal":
	- WinExec
	- CreateProcessAsUser
*/
BOOL MyShellExecuteExW(LPSHELLEXECUTEINFOW pExecInfo); //not catched because createprocess
LPVOID WINAPI MyVirtualAlloc(LPVOID lpAddress,SIZE_T dwSize,DWORD flAllocationType,DWORD flProtect); //not hooked
UINT WINAPI MyWinExec(LPCSTR lpCmdLine,UINT uCmdShow); //not hooked

HANDLE WINAPI MyOpenProcess(DWORD dwDesiredAccess,BOOL bInheritHandle,DWORD dwProcessId);
BOOL WINAPI MyCreateProcess(LPTSTR lpApplicationName,LPTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFO lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation);
BOOL WINAPI MyCreateProcessW(LPCWSTR lpApplicationName,LPWSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCWSTR lpCurrentDirectory,LPSTARTUPINFOW lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation);
DWORD WINAPI MyResumeThread(HANDLE hThread);
BOOL WINAPI MyWriteProcessMemory(HANDLE hProcess,LPVOID lpBaseAddress,LPCVOID lpBuffer,SIZE_T nSize,SIZE_T *lpNumberOfBytesWritten);
HANDLE WINAPI MyCreateRemoteThread(HANDLE hProcess,LPSECURITY_ATTRIBUTES lpThreadAttributes,SIZE_T dwStackSize,LPTHREAD_START_ROUTINE lpStartAddress,LPVOID lpParameter,DWORD dwCreationFlags,LPDWORD lpThreadId);
BOOL WINAPI MyCreateProcessInternal(DWORD unknown1, LPCTSTR lpApplicationName, LPCTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFO lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation,DWORD unknown2);
BOOL WINAPI MyCreateProcessAsUser(HANDLE hToken,LPCTSTR lpApplicationName,LPTSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCTSTR lpCurrentDirectory,LPSTARTUPINFO lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation);
BOOL WINAPI MyCreateProcessAsUserW(HANDLE hToken,LPCWSTR lpApplicationName,LPWSTR lpCommandLine,LPSECURITY_ATTRIBUTES lpProcessAttributes,LPSECURITY_ATTRIBUTES lpThreadAttributes,BOOL bInheritHandles,DWORD dwCreationFlags,LPVOID lpEnvironment,LPCWSTR lpCurrentDirectory,LPSTARTUPINFOW lpStartupInfo,LPPROCESS_INFORMATION lpProcessInformation);

