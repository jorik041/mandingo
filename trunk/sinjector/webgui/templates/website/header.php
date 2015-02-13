<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Mandingo's Sandbox - Malware Analysis</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="">

	<!--link rel="stylesheet/less" href="less/bootstrap.less" type="text/css" /-->
	<!--link rel="stylesheet/less" href="less/responsive.less" type="text/css" /-->
	<!--script src="js/less-1.3.3.min.js"></script-->
	<!--append ‘#!watch’ to the browser URL, then refresh the page. -->
	
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
  <![endif]-->

  <!-- Fav and touch icons -->
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="img/apple-touch-icon-144-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="img/apple-touch-icon-114-precomposed.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="img/apple-touch-icon-72-precomposed.png">
  <link rel="apple-touch-icon-precomposed" href="img/apple-touch-icon-57-precomposed.png">
  <link rel="shortcut icon" href="img/favicon.ico">
  
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/scripts.js"></script>

</head>
<body>
<div class="container">
	<div class="row clearfix">
		<div class="col-md-12 column">
			<div class="page-header" style="margin-top:5px">
				<table width=100%><tr><td>
				<div id="fullsize"></div>
				<h2>
					mandingo's&box <font size=2>beta 1.0</font> <small>Malware Analysis</small>
				</h2>
				</td><td align=right>
					<?if(isset($vars["md5"]) && strlen($vars["md5"]) && file_exists("art/".$vars["md5"]."-2.png")){?>
						<a href="?report=<?=$vars["md5"]?>&op=tools&app=radare2/radare2&fun=aa;af;afl"><img src="art/<?=$vars["md5"]?>-2.png" height=64 alt="Art"/></a>
					<?}else{?>
						<img src="http://lorempixel.com/64/64/" class="img-circle" height=64/>
					<?}?>
				</td></tr></table>
			</div>
			<ul class="nav nav-tabs">
				<li class="<?=(!isset($_GET["samples"]) && !isset($_GET["contact"]) && !isset($_GET["analyze"]) && !isset($_GET["report"]))?"active":""?>">
					<a href="?home">Submit sample (.exe)</a>
				</li>
				<li class="<?=isset($_GET["samples"])?"active":""?>">
					<a href="?samples">Samples</a>
				</li>
				<li class="<?=isset($_GET["contact"])?"active":""?>">
					<a href="?contact">Contact</a>
				</li>
				<li class="<?=isset($_GET["analyze"])?"active":"disabled"?>">
					<a href="#">Analysis</a>
				</li>
				<li class="<?=isset($_GET["report"])?"active":"disabled"?>">
					<a href="#">Report</a>
				</li>
				<li class="dropdown pull-right">
					 <a href="#" data-toggle="dropdown" class="dropdown-toggle">Dropdown<strong class="caret"></strong></a>
					<ul class="dropdown-menu">
						<li>
							<a href="?home">Home</a>
						</li>
						<li>
							<a href="?samples">View samples</a>
						</li>
						<li class="divider">
						</li>
						<li>
							<a href="?contact">Contact</a>
						</li>
					</ul>
				</li>
			</ul>
