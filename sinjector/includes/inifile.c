#include <windows.h>
#include <string.h>
#include <stdio.h>
#include "inifile.h"
#define DEBUG_INI 0

iniFile *parseIni(char *filename){
	static iniFile ini;
	FILE *in=fopen(filename,"rt");
	char line[1024];
	*ini.monitor=0;
	strncpy(ini.logfile,"c:\\inj_log.txt",sizeof(ini.logfile));
	strncpy(ini.iatfile,"c:\\inj_iat.txt",sizeof(ini.iatfile));
	ini.debuglevel=0;
	if(in==NULL || !strlen(filename)){
#if DEBUG_INI
		printf("INI file '%s' not found...\n",filename);
#endif
		return &ini;
	}
#if DEBUG_INI
	printf("[ini] Parsing INI file: %s\n",filename);
#endif
	while(!feof(in)){
		memset(line,0,sizeof(line));
		fgets(line,sizeof(line),in);
		if(!strncmp(line,"dll=",4)) {
			strncpy(ini.dll,line+4,sizeof(ini.dll));
			if(ini.dll[strlen(ini.dll)-1]=='\n' || ini.dll[strlen(ini.dll)-1]=='\r') ini.dll[strlen(ini.dll)-1]=0;
		}
		if(!strncmp(line,"monitor=",8)) {
			strncpy(ini.monitor,line+8,sizeof(ini.monitor));
			if(ini.monitor[strlen(ini.monitor)-1]=='\n' || ini.monitor[strlen(ini.monitor)-1]=='\r') ini.monitor[strlen(ini.monitor)-1]=0;
		}
		if(!strncmp(line,"logfile=",8)) {
			strncpy(ini.logfile,line+8,sizeof(ini.logfile));
			if(ini.logfile[strlen(ini.logfile)-1]=='\n' || ini.logfile[strlen(ini.logfile)-1]=='\r') ini.logfile[strlen(ini.logfile)-1]=0;
		}
		if(!strncmp(line,"iatfile=",8)) {
			strncpy(ini.iatfile,line+8,sizeof(ini.iatfile));
			if(ini.iatfile[strlen(ini.iatfile)-1]=='\n' || ini.iatfile[strlen(ini.iatfile)-1]=='\r') ini.iatfile[strlen(ini.iatfile)-1]=0;
		}
		if(!strncmp(line,"backup=",7)) {
			strncpy(ini.backup,line+7,sizeof(ini.backup));
			if(ini.backup[strlen(ini.backup)-1]=='\n' || ini.backup[strlen(ini.backup)-1]=='\r') ini.backup[strlen(ini.backup)-1]=0;
		}
		if(!strncmp(line,"debuglevel=",11)) {
			sscanf(line+11,"%d",&ini.debuglevel);
		}
		if(!strncmp(line,"reinject=",9)) {
			sscanf(line+9,"%d",&ini.reinject);
		}
		if(!strncmp(line,"reinject_blacklist=",19)) {
			strncpy(ini.reinject_blacklist,line+19,sizeof(ini.reinject_blacklist));
			if(ini.reinject_blacklist[strlen(ini.reinject_blacklist)-1]=='\n' || ini.reinject_blacklist[strlen(ini.reinject_blacklist)-1]=='\r') ini.reinject_blacklist[strlen(ini.reinject_blacklist)-1]=0;
		}
	}
	fclose(in);
	return &ini;
}
