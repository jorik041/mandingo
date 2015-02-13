<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/jquery.canvasjs.min.js"></script>
<?
$i=array();
$i["scale"]=1;
$i["rcount"]=count($vars["resources"]);

$i["size_declared"]=0;
foreach($vars["resources"] as $r) $i["size_declared"]+=$r["per_resource"];
$i["filesize"]=$vars["filesize"];

$i["scale"]=1;
//print_r($vars["resources"]);
//print $i["size_declared"]/100;
//print $vars["res_size"];
//print_r($vars["res_comp"]);
$i["section_chunks"]=count($vars["res_comp"]);
?>
<canvas id="canvas"></canvas>

<script>
var r_per=Array();
var r_type=Array();
var s_comp=Array();
var r_size=Array();
var icons_total=0;
<?
$n=0;
foreach($vars["resources"] as $r){
?>
r_size[<?=$n?>]=<?=$r["size"]["value"]?>;
r_per[<?=$n?>]=<?=$r["per_resource"]?>;
r_type[<?=$n?>]="<?=$r["type"]["value"]?>";
if(r_type[<?=$n?>]=="RT_ICON") icons_total++;
<?
$n++;
}
$n=0;
foreach($vars["res_comp"] as $c){
?>
s_comp[<?=$n?>]=<?=$c["y"]?>;
<?
$n++;
}
?>
var biggest=0;
var per_max=0;
for(i=0;i<<?=$i["rcount"]?>;i++){
	if(r_per[i]>=per_max) {
		per_max=r_per[i];
		biggest=i;
	}
}
canvas = document.getElementById("canvas");
//width and height
var scale=<?=$i["scale"]?>;
var canvas_width=window.innerWidth*0.675*scale;
var canvas_height=window.innerHeight*0.6*scale;
var ctx = $('#canvas')[0].getContext("2d");

canvas.width=canvas_width;
canvas.height=canvas_height;

//top sky (clean if no resources found)
var sky_level=188;
ctx.beginPath();
ctx.rect(0, 0, canvas_width, canvas_height*<?=$i["size_declared"]?>/sky_level);
ctx.fillStyle = "#667";
ctx.fill();
//middle sky
ctx.beginPath();
ctx.rect(0, canvas_height/2, canvas_width, canvas_height);
ctx.fillStyle = "#8da";
ctx.fill();
//bottom sky
ctx.beginPath();
ctx.rect(0, canvas_height*<?=$i["size_declared"]?>/sky_level, canvas_width, canvas_height);
ctx.fillStyle = "#092";
ctx.fill();

//eye
eye_width=<?=$i["filesize"]?>/1020;
eye_left=eye_width-(<?=-3*$i["rcount"]/2?>);
eye_top=eye_width-(<?=$i["rcount"]?>);
ctx.beginPath();
ctx.strokeStyle ="";
ctx.fillStyle="#baa";
ctx.arc(eye_left, eye_top, eye_width, 0, Math.PI*2, true); 
ctx.closePath();
ctx.fill();
//horizontal bar
if(<?=$i["size_declared"]?>>0){
	bar_left=canvas_width*<?=$i["size_declared"]?>/100;
	ctx.beginPath();
	bar_step=(canvas_width-bar_left)/<?=$i["section_chunks"]?>;
	for(j=0;j<<?=$i["section_chunks"]?>;j++){
		ctx.rect(bar_left+j*bar_step,0,bar_step-2,canvas_height);
		ctx.fillStyle = "rgba(120,110,100,"+(s_comp[j]-0.2)+")";
		ctx.fill();
	}
}
//eye iris
eye_width=<?=$i["filesize"]?>/<?=$i["size_declared"]?>/100;
eye_top-=(<?=$i["size_declared"]?>/20);
eye_left+=(<?=$i["size_declared"]?>/10);
ctx.beginPath();
ctx.strokeStyle ="";
ctx.fillStyle="rgba(0,0,0,0.5)";
ctx.arc(eye_left, eye_top, eye_width, 0, Math.PI*2, true); 
ctx.closePath();
ctx.fill();

//resources boxes
h_margin=10
step=(canvas_width-h_margin)/<?=$i["rcount"]?>;
box_hscale=1.22;
for(i=0;i<<?=$i["rcount"]?>;i++){
	x=i*step+h_margin;
	y_offset=(r_per[i]<100?r_per[i]:100)*canvas_height/620;
	box_height=(15+step*r_per[i]/50);
	//rcdata at far horizont
	if(r_type[i]=="RT_RCDATA"){
		y_offset+=canvas_height/2;
	}
	//wall
	b_width=5+step*r_per[i]/100*box_hscale;
	b_width=step;
	wall_y=canvas_height-box_height*(1-0.35)-y_offset;
	//circle behind wall for biggest
	if(biggest==i){
		ctx.beginPath();
		ctx.fillStyle = "rgba(250,180,150,0.9)";
		ctx.arc(x+step/2, wall_y, step/3, 0, Math.PI, true); 
		ctx.closePath();
		ctx.fill();		
	}
	//wall box
	ctx.beginPath();
	ctx.rect(x,wall_y, b_width*0.98, box_height);
	ctx.fillStyle = "rgba(255,230,"+Math.round(255-255*r_per[i]/100)+",0.95)";
	//oversized?
	if(r_per[i]>100)
		ctx.fillStyle = "rgba(255,0,0,0.8)";
	if(r_type[i]!="RT_VERSION" && r_type[i]!="RT_MANIFEST"){
		ctx.fill();
	}else{
		//version and manifest with dashed lines and more transparent
		ctx.strokeStyle = "rgba(0,0,0,0.4)";
		ctx.setLineDash([2]);
		if(r_type[i]=="RT_MANIFEST") ctx.setLineDash([4]);
		ctx.stroke();
		ctx.fillStyle = "rgba(255,220,100,0.2)";
		ctx.fill();
	}
	//dashed walls for data
	if(r_type[i]=="RT_RCDATA"){
		ctx.lineWidth = 20;
		ctx.strokeStyle = "#882";
		ctx.setLineDash([8]);
		ctx.stroke();
		//inner wall box
		in_factor=r_size[i]/<?=$i["filesize"]?>/4;
		ctx.beginPath();
		inner_w=b_width*0.98-2*(10/in_factor);
		inner_h=box_height-2*(10/in_factor);
		if(inner_w<0) inner_w=0;
//		if(inner_h<0) inner_h=0;
		ctx.rect(x+10/in_factor,wall_y+10/in_factor, inner_w, inner_h);
		ctx.fillStyle = "rgba(80,80,"+Math.round(255-255*r_per[i]/100)+",1)";
		ctx.fill();
	}
	ctx.closePath();
	//left top box for RT_DIALOG (like app icon)
	if(r_type[i]=="RT_DIALOG"){
		ctx.beginPath();
		ctx.rect(x+2,wall_y+2, 4, 4);
		ctx.fillStyle = "rgba(20,20,20,0.5)";
		ctx.fill();
	}
	//box over the wall for RT_ICON
	if(r_type[i]=="RT_ICON" || r_type[i]=="RT_BITMAP" || r_type[i]=="RT_CURSOR"){
		b_width=5+step*r_per[i]/100*box_hscale;
		b_width=step;
		ctx.beginPath();
		ctx.rect(x+step*0.23,canvas_height-(box_height-(box_height*(1-0.35)-y_offset)), b_width*(1-0.24*2), box_height/2);
		ctx.fillStyle = "rgba(155,130,"+Math.round(255-200*r_per[i]/100)+",1)";
		if(r_type[i]=="RT_CURSOR") ctx.fillStyle = "rgba(10,183,"+Math.round(255-200*r_per[i]/100)+",0.7)";
		if(r_type[i]=="RT_BITMAP") ctx.fillStyle = "rgba(155,13,"+Math.round(255-200*r_per[i]/100)+",0.7)";
		ctx.fill();
		ctx.closePath();
	}
	//box over the wall for RT_GROUP_ICON
	if(r_type[i]=="RT_GROUP_ICON"){
		b_width=5+step*r_per[i]/100*box_hscale;
		b_width=step;
		ctx.beginPath();
		ctx.rect(x+step*0.33,canvas_height-box_height*(1-0.35)-y_offset, b_width*0.33, -box_height*icons_total*2);
		ctx.fillStyle = "rgba(195,130,"+Math.round(255-200*r_per[i]/100)+",0.7)";
		ctx.fill();
		ctx.closePath();
	}
	//box over the wall for RT_GROUP_ICON
	if(r_type[i]=="RT_GROUP_CURSOR"){
		b_width=5+step*r_per[i]/100*box_hscale;
		b_width=step;
		ctx.beginPath();
		ctx.rect(x+step*0.33,canvas_height-box_height*(1-0.35)-y_offset, b_width*0.33, -box_height*2);
		ctx.fillStyle = "rgba(10,183,"+Math.round(255-200*r_per[i]/100)+",1)";
		ctx.fill();
		ctx.closePath();
	}
	if(r_type[i]=="RT_RCDATA"){
		ctx.lineWidth = 4;
		ctx.strokeStyle = "#a72";
		ctx.setLineDash([0]);
		ctx.stroke();
	}
}
$.post("?submit_art", { md5: "<?=$vars["md5"]?>",type:"1", data: canvas.toDataURL()});

</script>
