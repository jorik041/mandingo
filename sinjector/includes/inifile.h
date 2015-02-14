#ifndef PINI
#define PINI
typedef struct{
	char dll[512];
	char monitor[512];
	char logfile[512];
	char iatfile[512];
	char backup[512];
	int debuglevel;
	int reinject;
	char reinject_blacklist[1024];
}iniFile;

iniFile *parseIni(char *filename);
#endif
