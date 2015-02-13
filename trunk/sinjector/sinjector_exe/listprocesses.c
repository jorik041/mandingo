#include <windows.h>
#include <tlhelp32.h>
#include <stdio.h>
#include <string.h>

void lower_string(char *string)
{
   while(*string)
   {
      if ( *string >= 'A' && *string <= 'Z' ) 
      {
         *string = *string + 32;
      }
      string++;
   }
}

typedef struct _windows{
	int pid;
	char title[256];
}windows;

#define MAXWINDOWS 256
windows win[MAXWINDOWS];
int winCount=0;

BOOL CALLBACK EnumWindowsProc(HWND hwnd,LPARAM lparam){
	DWORD id;
	GetWindowThreadProcessId(hwnd,&id);
	GetWindowText(hwnd,(LPTSTR)win[winCount].title,sizeof(win[winCount].title));
	win[winCount].pid=id;
	winCount++;
	return TRUE;
}
char *getWinTitle(int pid){
	int i;
	for(i=0;i<winCount;i++) if(pid==win[i].pid) return win[i].title;
	return "";
}
int listProcesses(void){
   PROCESSENTRY32 pEntry;
   HANDLE pSnap;
	
	EnumWindows(&EnumWindowsProc,0);

   pSnap = CreateToolhelp32Snapshot(TH32CS_SNAPPROCESS, 0);
   if (pSnap == INVALID_HANDLE_VALUE){
		printf("listProcesses: Error 0001\n");
      return 0;
	}
	pEntry.dwSize = sizeof(PROCESSENTRY32);
   if (!Process32First(pSnap, &pEntry)){
		printf("listProcesses: Error 0002\n");
      return 0;
	}

   while(Process32Next(pSnap, &pEntry))
   {
		char tmp[64];
		strncpy(tmp,pEntry.szExeFile,sizeof(tmp));
  		lower_string(tmp);
		LPTSTR title;
		title=LocalAlloc(0,32);
		printf("[%-6d] %-16s # %s\n",pEntry.th32ProcessID,tmp,getWinTitle(pEntry.th32ProcessID));
	}
	return 0;
}
/*
int main(int argc,char**argv){
	char proc[]="SERVICES.EXE";
	printf("%d",checkProcess(proc));
}*/
