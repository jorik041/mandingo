char *getfile_fullpath(char *file);
char *unicode2ascii(WCHAR *unicode);
char *getcmdlinefrompid(int pid);
BOOL inBlackList(char *string,char *blacklist);
char *filename(char *fullpath);
BOOL lockAccess(int pid);
void unlockAccess(int pid);
int STRCMP(char *string1,char *string2);
