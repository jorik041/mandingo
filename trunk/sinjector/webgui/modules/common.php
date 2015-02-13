<?
class Common{
        static function getString($varname){
                $value=isset($_GET[$varname])?$_GET[$varname]:"";
                return $value;
        }
        static function getInteger($varname){
				if(!isset($_GET[$varname])) return "";
                return intval($_GET[$varname]);
        }
        static function getIntegerPost($varname){
				if(!isset($_POST[$varname])) return "";
                return intval($_POST[$varname]);
        }
        static function getPost($varname){
                $value=isset($_POST[$varname])?$_POST[$varname]:"";
                return $value;
        }
        static function getMD5($varname){
                $value=self::getString($varname);
				if(strlen($value)) {
					preg_match("/([0-9a-f]+)/i",$value,$c);
					if(count($c)) $value=$c[1];
				}
                return $value;
        }
        static function error($title,$description){
                $html=Templates::Load("templates/website/error.php",array("title"=>$title,"description"=>$description));
                return $html;
        }
        static function info($title,$description){
                $html=Templates::Load("templates/website/info.php",array("title"=>$title,"description"=>$description));
                return $html;
        }
        static function redir($page){
                header("Location: $page");
                die;
        }
}
