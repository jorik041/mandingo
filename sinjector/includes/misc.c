#include <windows.h>
#include <stdio.h>
#include <Shlwapi.h>

int STRCMP(char *string1,char *string2){
	char *p=string1,*q=string2;
	while(*p && *q){
		if(*p>*q) return 1;
		if(*q>*p) return -1;
		p++;
		q++;
	}
	return 0;
}
BOOL lockAccess(int pid){
	char mname[64];
	sprintf(mname,"inj_open_%d",pid);
	CreateMutex(NULL,TRUE,mname);
	return GetLastError()?FALSE:TRUE;
}
void unlockAccess(int pid){
	char mname[64];
	sprintf(mname,"inj_open_%d",pid);
	CloseHandle(OpenMutex(SYNCHRONIZE,TRUE,mname));
}

char *filename(char *fullpath){
	static char *p;
	p=fullpath+strlen(fullpath)-1;
	while(*p){
		//printf(">>%s\n",p);
		if(*p=='\\') break;
		p--;
	}
	return p+1;
}
BOOL inBlackList(char *string,char *blacklist){
	BOOL found=FALSE;
    char buf[100];
    const char *data=blacklist;

    // now we want an array of pointer to fields, 
    // here again we use a static buffer for simplicity,
    // instead of a char** with a malloc
    char * fields[10];
    int nbfields = 0;
    int i = 0;
    char * start_of_field;

    // let's initialize buf with some data, 
    // a semi-colon terminated list of strings 
    strcpy(buf, data);

    start_of_field = buf;
    // seek end of each field and
    // copy addresses of field to fields (array of char*)
    for (i = 0; buf[i] != 0; i++){
        if (buf[i] == '|'){
            buf[i] = 0;
            fields[nbfields] = start_of_field;
            nbfields++;
            start_of_field = buf+i+1;
        }
    }
	fields[nbfields++]=start_of_field;//last field
	for(i=0;i<nbfields;i++){
		//printf("%s - %s\n",string,fields[i]);
		if(StrStrI(string,fields[i])) found=TRUE;
	}
	return found;
}

char *getfile_fullpath(char *file){
    //static char fullPath[MAX_PATH],longPath[MAX_PATH];
	//char *sortName;
	return file;//QUE FALLA?? (chrome casca)
   	/*GetFullPathName(file, sizeof(fullPath), fullPath, &sortName);
	if(GetLongPathName(fullPath,longPath,sizeof(longPath))) return longPath;
	return file;*/
}
char *unicode2ascii(WCHAR *unicode){
	static char ascii[1024];
	memset(ascii,0,sizeof(ascii));
	wcstombs(ascii,unicode,sizeof(ascii));
	return ascii;
}

typedef NTSTATUS (NTAPI *_NtQueryInformationProcess)(
    HANDLE ProcessHandle,
    DWORD ProcessInformationClass,
    PVOID ProcessInformation,
    DWORD ProcessInformationLength,
    PDWORD ReturnLength
    );

typedef struct _UNICODE_STRING
{
    USHORT Length;
    USHORT MaximumLength;
    PWSTR Buffer;
} UNICODE_STRING, *PUNICODE_STRING;

typedef struct _PROCESS_BASIC_INFORMATION
{
    LONG ExitStatus;
    PVOID PebBaseAddress;
    ULONG_PTR AffinityMask;
    LONG BasePriority;
    ULONG_PTR UniqueProcessId;
    ULONG_PTR ParentProcessId;
} PROCESS_BASIC_INFORMATION, *PPROCESS_BASIC_INFORMATION;

PVOID GetPebAddress(HANDLE ProcessHandle)
{
    _NtQueryInformationProcess NtQueryInformationProcess =
        (_NtQueryInformationProcess)GetProcAddress(
        GetModuleHandleA("ntdll.dll"), "NtQueryInformationProcess");
    PROCESS_BASIC_INFORMATION pbi;

    NtQueryInformationProcess(ProcessHandle, 0, &pbi, sizeof(pbi), NULL);

    return pbi.PebBaseAddress;
}
char *getcmdlinefrompid(int pid)
{
    HANDLE processHandle;
    PVOID pebAddress;
    PVOID rtlUserProcParamsAddress;
    UNICODE_STRING commandLine;
    WCHAR *commandLineContents;

    if ((processHandle = OpenProcess(
        PROCESS_QUERY_INFORMATION | /* required for NtQueryInformationProcess */
        PROCESS_VM_READ, /* required for ReadProcessMemory */
        FALSE, pid)) == 0)
    {
        printf("Could not open process!\n");
        return "";
    }

    pebAddress = GetPebAddress(processHandle);

    /* get the address of ProcessParameters */
    if (!ReadProcessMemory(processHandle, (PCHAR)pebAddress + 0x10,
        &rtlUserProcParamsAddress, sizeof(PVOID), NULL))
    {
        printf("Could not read the address of ProcessParameters!\n");
        return "";
    }

    /* read the CommandLine UNICODE_STRING structure */
    if (!ReadProcessMemory(processHandle, (PCHAR)rtlUserProcParamsAddress + 0x40,
        &commandLine, sizeof(commandLine), NULL))
    {
        printf("Could not read CommandLine!\n");
        return "";
    }

    /* allocate memory to hold the command line */
    commandLineContents = (WCHAR *)malloc(commandLine.Length);
    /* read the command line */
    if (!ReadProcessMemory(processHandle, commandLine.Buffer,
        commandLineContents, commandLine.Length, NULL))
    {
        printf("Could not read the command line string!\n");
        return "";
    }

    CloseHandle(processHandle);
	static char ascii[1024];
	memset(ascii,0,sizeof(ascii));
	wcstombs(ascii,commandLineContents,commandLine.Length<sizeof(ascii)?commandLine.Length:sizeof(ascii));
	return ascii;
}
