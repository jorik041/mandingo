<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/jquery.canvasjs.min.js"></script>
<script>
function orange(){
	var x = canvas.width / 2;
	var y = canvas.height;
	var radius = 175;
	var startAngle = Math.PI;
	var endAngle = 2 * Math.PI;
	var counterClockwise = false;

	ctx.beginPath();
	ctx.arc(x, y, radius, startAngle, endAngle, counterClockwise);
	ctx.lineWidth = 15;

	// line color
	ctx.strokeStyle = 'rgba(150,50,20,0.7)';
	ctx.fillStyle="rgba(200,100,70,0.8)";
	ctx.fill();
	ctx.stroke();
}
</script>
<?
$i=array();
$i["scale"]=1;
$i["resources"]=array();
$i["rcount"]=count($vars["resources"]);
$i["scount"]=count($vars["sections"]);

$i["iris"]["color"]="black";
if($i["scount"]>=8) $i["iris"]["color"]="orange"; 

$i["res_section_index"]="";
$i["size_declared"]=0;
$n=0;
foreach($vars["sections"] as $s) {
	$i["size_declared"]+=$s["per_file"];
	if($s["dir"]=="RESOURCE") $i["res_section_index"]=$n;
	$n++;
}

$i["section0"]["color"]="black";
$i["section0"]["width"]=0;
if(isset($vars["sections"][0])){
	$i["section0"]["width"]=$vars["sections"][0]["per_file"];
	if($i["section0"]["width"]>100) {
		$i["section0"]["width"]=100;
	}
}
$i["section1"]["color"]="#600";
$i["section1"]["width"]=0;
if(isset($vars["sections"][1])){
	$i["section1"]["width"]=$vars["sections"][1]["per_file"];
}
$i["section2"]["width"]=0;
$i["section2"]["color"]="#a00";
if(isset($vars["sections"][2])){
	$i["section2"]["width"]=$vars["sections"][2]["per_file"];
	if($vars["sections"][2]["dir"]=="RESOURCE") $i["section2"]["color"]="#6e6";
}
$n=3;
if($i["res_section_index"]!="") $n=$i["res_section_index"];
$i["section3"]["color"]="white";
$i["section3"]["width"]=0;
if(isset($vars["sections"][$n])){
	$i["section3"]["width"]=$vars["sections"][$n]["per_file"];
	if($vars["sections"][$n]["dir"]=="RESOURCE") $i["section3"]["color"]="#6e6";
}

if(isset($vars["sections"][0]) && $vars["sections"][0]["srd"]["intelligence"]["class"]=="warning") $i["section0"]["color"]="orange";
if(isset($vars["sections"][0]) && $vars["sections"][0]["srd"]["intelligence"]["class"]=="danger") $i["section0"]["color"]="red";

foreach($vars["resources"] as $r){
	array_push($i["resources"],$r["type"]["intelligence"]["class"]);
}
//print_r($i["resources"]);

$canvas["background"]="#".sprintf("%2X%2X%2X",$i["rcount"]*20,$i["rcount"]*20,$i["rcount"]*20);
$circle["width"]=$i["scount"];
$circle["color"]=array();
$circle["color"]["r"]=$i["scount"]*10;
$circle["color"]["g"]=$i["scount"]*20;
$circle["color"]["b"]=$i["scount"]*30;
if($circle["color"]["r"]>255) $circle["color"]["r"]=255;
if($circle["color"]["g"]>255) $circle["color"]["g"]=255;
if($circle["color"]["b"]>255) $circle["color"]["b"]=255;
$circle["color"]["rgb"]="rgba(".$circle["color"]["r"].",".$circle["color"]["g"].",".$circle["color"]["b"].",0.95)";
//print $circle["color"]["rgb"];
$banner="";
foreach($vars["version"] as $v){
	if($v["name"]=="FileDescription") {$banner=$v["value"];break;}
	if($v["name"]=="ProductName") $banner=$v["value"];
}
?>
<canvas id="canvas"></canvas>

<script>
var s_per=Array();
var s_name=Array();
var s_dir=Array();
var s_ent=Array();
var s_flags=Array();
<?

$n=0;
foreach($vars["sections"] as $s){
?>
s_per[<?=$n?>]=<?=$s["per_file"]?>;
s_name[<?=$n?>]="<?=$s["name"]?>";
s_flags[<?=$n?>]="<?=$s["flags"]?>";
s_dir[<?=$n?>]="<?=$s["dir"]?>";
s_ent[<?=$n?>]=<?=$s["entropy"]["value"]?>;
<?
$n++;
}?>

canvas = document.getElementById("canvas");
//width and height
var scale=<?=$i["scale"]?>;
var canvas_width=window.innerWidth*0.675*scale;
var canvas_height=window.innerHeight*0.6*scale;
var ctx = $('#canvas')[0].getContext("2d");

canvas.width=canvas_width;
canvas.height=canvas_height;

//top sky
var sky_level=188;
ctx.beginPath();
ctx.rect(0, 0, canvas_width, canvas_height*<?=$i["size_declared"]?>/sky_level);
ctx.fillStyle = "<?=$circle["color"]["rgb"]?>";
ctx.fill();

//background circle
var circle_left=canvas_width/1.21;
var circle_top=canvas_height/2.4;
var circle_width=canvas_height*2/<?=$circle["width"]?>;
var circle_height=0;
if(<?=$circle["color"]["r"]?>>=80) circle_height+=<?=$i["scount"]?>*1.3;
if(<?=$circle["color"]["g"]?>>=120) circle_left*=0.8;
if(<?=$circle["color"]["b"]?>>=170) circle_top*=1.2;
ctx.beginPath();
//circle fill section 3
ctx.lineWidth = 0;
ctx.fillStyle = "<?=$i["section3"]["color"]?>";
ctx.arc(circle_left, circle_top, circle_width*18*(<?=$i["section3"]["width"]?>/100), 0, Math.PI*2, true); 
ctx.fill();
ctx.closePath();
//fill s2
ctx.beginPath();
ctx.fillStyle = "<?=$i["section2"]["color"]?>";
ctx.arc(circle_left, circle_top, 120+circle_width*6*(<?=$i["section2"]["width"]?>/100), 0, Math.PI*2, true); 
ctx.fill();
ctx.closePath();
//fill s1
ctx.beginPath();
ctx.fillStyle = "<?=$i["section1"]["color"]?>";
ctx.arc(circle_left, circle_top,50+circle_width*3*(<?=$i["section1"]["width"]?>/100),0, Math.PI*2, true); 
ctx.fill();
ctx.closePath();
//fill s0 (iris)
ctx.beginPath();
ctx.strokeStyle ="";
ctx.fillStyle="<?=$i["section0"]["color"]?>";
ctx.arc(circle_left, circle_top, 10+circle_width/2*(<?=$i["section0"]["width"]?>/100), 0, Math.PI*2, true); 
ctx.fill();
ctx.closePath();
ctx.beginPath();
ctx.fillStyle="black";
ctx.arc(circle_left, circle_top, circle_width/4*(<?=$i["section0"]["width"]?>/100), 0, Math.PI*2, true); 
ctx.fill();
ctx.closePath();
//bottom sky
ctx.beginPath();
ctx.rect(0, canvas_height*<?=$i["size_declared"]?>/sky_level, canvas_width, canvas_height);
ctx.fillStyle = "rgba(150,180,255,0.8)";
ctx.fill();

<?//print Art::banner(array("text"=>$banner,"x"=>0,"y"=>0,"color"=>"#aef","bordercolor"=>"#000","size"=>"80pt"));?>

//bottom boxes
h_margin=30
step=(canvas_width-h_margin)/<?=$i["scount"]?>;
box_hscale=0.87;
for(i=<?=$i["scount"]-1?>;i>=0;i--){
	x=i*step+h_margin;
	y_offset=15*s_ent[i]*s_per[i]/115+35;
	box_height=(5+step*s_per[i]/190);
	//ceil
	ceil_top=canvas_height-box_height-y_offset;
	ceil_height=box_height*0.35;
	ctx.beginPath();
	ctx.rect(x,ceil_top, 5+step*s_per[i]/100*box_hscale, ceil_height);
	ctx.fillStyle = "rgba("+Math.round(255-200*s_per[i]/100)+",60,0,1)";
	ctx.fill();
	ctx.closePath();
	//stick
	flag_size=20;
	stick_size=flag_size*3;
	flag_pos=0.9*(stick_size-(s_ent[i]/7)*stick_size);
	ctx.beginPath();
	ctx.rect(x-2,ceil_top-stick_size, 2,stick_size+box_height+ceil_height);
	ctx.fillStyle = "rgba(180,140,50,1)";
	ctx.fill();
	ctx.closePath();
	//exec flag
	if(s_flags[i].match("exec")) {
		//flag
		ctx.beginPath();
		ctx.rect(x,ceil_top-stick_size+flag_pos, flag_size,flag_size);
		ctx.fillStyle = "rgba(255,0,0,1)";
		ctx.fill();
		ctx.closePath();
	}
	//data flag
	if(s_flags[i].match("data")) {
		flag_size=20;
		//flag
		ctx.beginPath();
		ctx.rect(x,ceil_top-stick_size+flag_pos, flag_size,flag_size*0.5);
		ctx.fillStyle = "rgba(255,155,0,1)";
		ctx.fill();
		ctx.closePath();
	}
	//code flag
	if(s_flags[i].match("code")) {
		//ball
		ball_size=4;
		ctx.beginPath();
		ctx.arc(x+ball_size/4-2, ceil_top-stick_size, ball_size, 0, Math.PI*2, true); 
		ctx.fillStyle = "rgba(240,240,0,1)";
		ctx.fill();
		ctx.closePath();
	}
	//wall
	ctx.beginPath();
	ctx.rect(x,canvas_height-box_height*(1-0.35)-y_offset, 5+step*s_per[i]/100*box_hscale, box_height);
	ctx.fillStyle = "rgba(255,230,"+Math.round(255-255*s_per[i]/100)+",1)";
	ctx.fill();
	ctx.closePath();
	font_size=ceil_height;
	//title topleft
	ctx.fillStyle = '#bbb';
	ctx.textAlign = 'left';
	ctx.font = font_size+'pt Calibri';
    ctx.fillText(s_name[i], x+5, ceil_top+font_size);
	//title bottom
	door_top=canvas_height-box_height*0.1-y_offset;
	ctx.font = font_size+'pt Calibri';
	if(s_dir[i]!=""){
		ctx.fillStyle = '#500';
		ctx.textAlign = 'center';
		ctx.fillText("[ "+s_dir[i]+" ]", x+box_height/2*box_hscale, ceil_top+ceil_height+font_size);
	}
	//bottom box dark door entrance
	ctx.beginPath();
	ctx.textAlign = 'center';
	ctx.fillStyle = "rgba(0,0,0,0.8)";
	ctx.rect(x+box_height*0.1,door_top, box_height*0.8*box_hscale,box_height)//canvas_height-door_top);
	ctx.fill();
	ctx.closePath();
	ctx.beginPath();
	ctx.fillStyle = "rgba(0,0,0,0.3)";
	ctx.rect(x+box_height*0.1,door_top+box_height, box_height*0.8*box_hscale,canvas_height-(door_top+box_height));
	ctx.fill();
	ctx.closePath();
	//bottom box gray base
	ctx.beginPath();
	ctx.fillStyle = "gray";
	ctx.rect(x+box_height*0.1,door_top, box_height*0.8*box_hscale,box_height*0.05);
	ctx.fill();
	ctx.closePath();
	//house base 1
	ctx.beginPath();
	ctx.rect(x-5*s_ent[i]+10,canvas_height-box_height*(1-0.35)-y_offset+box_height, 5+step*s_per[i]/100*box_hscale+10*s_ent[i]-20, box_height);
	if(s_dir[i]=="RESOURCE"){
		ctx.fillStyle = "rgba(55,"+Math.round(255-150*s_ent[i]/8)+",10,1)";
	}else{
		ctx.fillStyle = "rgba("+Math.round(255-150*s_ent[i]/8)+",55,10,1)";
	}
	ctx.fill();
	ctx.closePath();
	//house base 2
	ctx.beginPath();
	ctx.rect(x-5*s_ent[i]+10,canvas_height-box_height*(1-0.35)-y_offset+box_height*2-1, 5+step*s_per[i]/100*box_hscale+10*s_ent[i]-20, box_height);
	if(s_dir[i]=="RESOURCE"){
		ctx.fillStyle = "rgba(100,"+Math.round(255-150*s_ent[i]/8)+",100,1)";
	}else{
		ctx.fillStyle = "rgba("+Math.round(255-150*s_ent[i]/8)+",100,100,1)";
	}
	ctx.fill();
	ctx.closePath();
	font_size=(s_per[i]/4);
}
<?if(preg_match("/Mono/",$vars["magic"])){?>
orange();
<?}?>

/*
//grayscale effect
imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
px = imageData.data;
length = px.length;

for (; i < length; i += 4) {
	gray = px[i] * .43 + px[i + 1] * .23 + px[i + 2] * .23;
	px[i] = px[i + 1] = px[i + 2] = px[i+3]=gray;
}
		
ctx.putImageData(imageData, 0, 0);
*/

$.post("?submit_art", { md5: "<?=$vars["md5"]?>",type:"0", data: canvas.toDataURL()});
</script>
