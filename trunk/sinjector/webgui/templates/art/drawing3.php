<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/jquery.canvasjs.min.js"></script>
<?
$i=array();
$i["scale"]=1;
$i["fcount"]=count($vars["functions"]);
$i["functions"]=$vars["functions"];
$i["codesize"]=$vars["codesize"];
$i["entrypoint"]=$vars["entrypoint"];
?>
<canvas id="canvas"></canvas>

<script>
var f_size=Array();
var f_type=Array();
var f_cc=Array();
var f_incodesec=Array();
var fcount=0;
var f_entrypoint=0;
<?
$n=0;
$size_declared=0;
foreach($i["functions"] as $f){
?>
f_size[<?=$n?>]=<?=$f["size"]?>;
f_type[<?=$n?>]="<?=$f["type"]?>";
f_cc[<?=$n?>]=<?=$f["cc"]?>;
f_incodesec[<?=$n?>]=<?=$f["incodesec"]?>;
fcount++;
<?
if($f["address"]==$i["entrypoint"]) print "f_entrypoint=$n;\n";
$size_declared+=$f["size"];
$n++;
}?>
var size_declared=<?=$size_declared?>;
var code_size=<?=$i["codesize"]?>;
canvas = document.getElementById("canvas");
//width and height
var scale=<?=$i["scale"]?>;
var canvas_width=window.innerWidth*0.675*scale;
var canvas_height=window.innerHeight*0.6*scale;
var ctx = $('#canvas')[0].getContext("2d");

canvas.width=canvas_width;
canvas.height=canvas_height;
draw_background();
draw_box();
draw_boxes();
function draw_box(){
	y=0;
	b_width=canvas_height-fcount*10;
	b_height=b_width*0.8+fcount*3;
	if(b_width<10) b_width=10;
	x=(canvas_width-b_width)/2;
	ctx.beginPath();
	ctx.rect(x,y, b_width, b_height);
	ctx.fillStyle = "rgba(205,200,200,"+(fcount*0.2)+")";
	ctx.fill();	
}
function draw_boxes(){
	h_margin=10
	step=(canvas_width-h_margin)/fcount;
	box_hscale=1.22;
	for(i=0;i<fcount;i++){
		f_per=f_size[i]/size_declared;
		x=i*step+h_margin;
		y_offset=f_per*canvas_height/1.1;
		box_height=(15+step*f_per/50);
		wall_y=canvas_height-box_height*(1-0.35)-y_offset;
		b_width=step;

		//if function is entrypoint... vertical light
		if(i==f_entrypoint){
			ctx.beginPath();
			ctx.fillStyle = "rgba(220,220,220,.2)";
			ctx.rect(x,0,b_width, canvas_height);
			ctx.fill();
			ctx.closePath();
		}

		//bubble circle
		ctx.beginPath();
		ctx.fillStyle="rgba(255,255,255,"+(f_cc[i]/80)+")";
		ctx.arc(x+b_width/2, wall_y+box_height*f_per*0.8, box_height*f_per*10, 0, Math.PI*2, true); 
		ctx.fill();
		ctx.closePath();


		//main box
		ctx.beginPath();
		ctx.rect(x,wall_y, b_width*0.98, box_height);
		if(f_type[i]=="fcn"){
			ctx.fillStyle = "rgba(255,230,"+Math.round(255-255*f_per*10)+",0.95)";
			ctx.fill();
		}else{
			ctx.lineWidth = 3;
			ctx.strokeStyle = "rgba(255,"+Math.round(255-255*f_per/100)+",230,0.95)";
			ctx.stroke();
		}
		ctx.closePath();
		//mark if inside code section
		if(f_incodesec[i]){
			ctx.beginPath();
			ctx.rect(x+b_width*0.3,wall_y+box_height*0.3, b_width*0.98-+b_width*0.6, box_height-box_height*0.3);
			ctx.fillStyle = "rgba(150,0,0,0.25)";
			ctx.fill();
			ctx.closePath();
		}

		//if function is entrypoint... canion
		if(i==f_entrypoint){
			canion_width=b_width/20;
			canion_height=box_height;
			ctx.beginPath();
			ctx.fillStyle = "rgba(255,255,255,0.8)";
			ctx.rect(x,wall_y-canion_height, canion_width, canion_height*2);
			ctx.rect(x+b_width*0.98,wall_y-canion_height, -canion_width, canion_height*2);
			ctx.fill();
			ctx.closePath();
		}

		//base box (complexity)
		basebox_y=wall_y+box_height;
		basebox_height=(box_height/8)*f_cc[i]/3;
		ctx.beginPath();
		var grd = ctx.createLinearGradient(x,basebox_y, x, basebox_y+basebox_height);
		grd.addColorStop(0, "rgba("+Math.round(255*4*f_per+20)+",0,0,1)");
		grd.addColorStop(0.4, "rgba(100,0,0,0.9)");   
		grd.addColorStop(0.8, "rgba(50,0,0,0.4)");   
		grd.addColorStop(1, "rgba(0,0,0,0.1)");   
		ctx.fillStyle = grd;
		ctx.rect(x,basebox_y, b_width*0.98, basebox_height);
		ctx.fill();
		ctx.closePath();

	}
}
function draw_background(){
	//top sky (clean if no resources found)
	var sky_level=188;
	var sky_height=canvas_height*<?=$i["fcount"]?>/sky_level;
	ctx.beginPath();
	ctx.rect(0, 0, canvas_width, sky_height);
	ctx.fillStyle = "#679";
	ctx.fill();
	ctx.closePath();
	//bottom sky
	ctx.beginPath();
	ctx.rect(0, sky_height, canvas_width, canvas_height-sky_height);
	ctx.fillStyle = "#8ab";
	ctx.fill();
	ctx.closePath();
	//hot horizon code_size/size_declared
	horizon_y=canvas_height-size_declared/code_size*canvas_height;
	ctx.beginPath();
	ctx.rect(0, 0, canvas_width, horizon_y);
	ctx.fillStyle = "rgba(220,70,0,"+(1-size_declared/code_size)+")";
	ctx.fill();
	ctx.closePath();

	//eye
	eye_width=<?=$i["fcount"]?>;
	eye_left=eye_width-(<?=-3*$i["fcount"]/2?>);
	eye_top=eye_width-(<?=$i["fcount"]?>);
	ctx.beginPath();
	ctx.strokeStyle ="";
	ctx.fillStyle="#baa";
	ctx.arc(eye_left, eye_top, eye_width, 0, Math.PI*2, true); 
	ctx.closePath();
	ctx.fill();
}
$.post("?submit_art", { md5: "<?=$vars["md5"]?>",type:"2", data: canvas.toDataURL()});

</script>
