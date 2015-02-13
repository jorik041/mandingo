<?
class Templates{
        static function Load($file,$vars){
                ob_start();
                include($file);
                $res=ob_get_contents();
                ob_end_clean();
                return $res;
        }
}
