#include <windows.h>
#include <stdio.h>
#include <process.h>
#include "../includes/inifile.h"
#include "inlinehook.h"
#include "../includes/logger.h"
#include <Shlwapi.h>
#include "../includes/misc.h"
#include <direct.h>

//#define SINJECTOR "C:\\Documents and Settings\\user\\My Documents\\Pelles C Projects\\dll_injection\\sinjector\\sinjector.exe"

#define SINJECTOR "C:\\sinjector\\sinjector.exe"
#define SINJECTOR_INI "C:\\sinjector\\injector.ini"
void hook(void);
void unhook(void);
iniFile *loadHookerIniFile(char *filename);

extern iniFile *ini;
