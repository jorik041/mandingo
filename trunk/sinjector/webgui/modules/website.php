<?
class Website{
        static function Page($file,$vars){
			$html=Templates::Load("templates/website/header.php",$vars);
			$html.=Templates::Load("templates/website/$file",$vars);
			$html.=Templates::Load("templates/website/footer.php",$vars);
			return $html;
        }
		static function Error($msg){
			return Templates::Load("templates/website/error.php",array("msg"=>$msg));
		}
}
