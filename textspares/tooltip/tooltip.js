function validateForm(){
   if(document.partform.make.value=="") {
      alert("Please select a Make.");
      document.partform.make.focus();
      return false;
   }
   if(document.partform.model.value=="") {
      alert("Please select a Model.");
      document.partform.model.focus();
      return false;
   }
   if(document.partform.year.value=="") {
      alert("Please select a Year.");
      document.partform.year.focus();
      return false;
   }
   if(document.partform.engine.value=="") {
      alert("Please select a Engine.");
      document.partform.engine.focus();
      return false;
   }
   if(document.partform.fuel.value=="") {
      alert("Please select a Fuel Type.");
      document.partform.fuel.focus();
      return false;
   }
   if(document.partform.trans.value=="") {
      alert("Please select a Transmission Type.");
      document.partform.trans.focus();
      return false;
   }
   if(document.partform.body.value=="") {
      alert("Please select a Body Type.");
      document.partform.body.focus();
      return false;
   }
   return true;
}

function validateVRM(){
   if(document.vrmform.vrm.value=="") {
      alert("Please Enter Your Number Plate To Find Parts");
      document.vrmform.vrm.focus();
      return false;
   }

   return true;
}

function go(loc) {
   window.location.href = loc;
}


/******* ADD PART CODE *******/

ContentInfo = "";

topColor = "#3e3f76"
subColor = "#EFEFEF"

var mouse_X;
var mouse_Y;

var tip_active = 0;

function update_tip_pos(){
	document.getElementById('ToolTip').style.left = mouse_X + 20 +"px";
	document.getElementById('ToolTip').style.top  = mouse_Y +"px";
	}

var ie = document.all?true:false;
if (!ie) document.captureEvents(Event.MOUSEMOVE)
document.onmousemove = getMouseXY;

function getMouseXY(e) {
if (ie) { // grab the x-y pos.s if browser is IE
mouse_X = event.clientX + document.body.scrollLeft;
mouse_Y = event.clientY + document.body.scrollTop;
}
else { // grab the x-y pos.s if browser is NS
mouse_X = e.pageX;
mouse_Y = e.pageY;
}
if (mouse_X < 0){mouse_X = 0;}
if (mouse_Y < 0){mouse_Y = 0;}

if(tip_active){update_tip_pos();}
}

function EnterContent(TTitle, TContent){

ContentInfo = '<table border="0" width="160" cellspacing="0" cellpadding="0">'+
'<tr><td width="100%" bgcolor="#000000">'+

'<table border="0" width="100%" cellspacing="1" cellpadding="0">'+
'<tr><td width="100%" bgcolor='+topColor+'>'+

'<table border="0" width="90%" cellspacing="0" cellpadding="0" align="center">'+
'<tr><td width="100%">'+

'<font style="color:#FFFFFF;"><b>&nbsp;'+TTitle+'</b></font>'+

'</td></tr>'+
'</table>'+

'</td></tr>'+

'<tr><td width="100%" bgcolor='+subColor+'>'+

'<table border="0" width="90%" cellpadding="0" cellspacing="1" align="center">'+

'<tr><td width="100%">'+

'<font style="color:#000000;">'+TContent+'</font>'+

'</td></tr>'+
'</table>'+

'</td></tr>'+
'</table>'+

'</td></tr>'+
'</table>';

}

function tip_it(which, TTitle, TContent){
	if(which){
		update_tip_pos();
		tip_active = 1;
		document.getElementById('ToolTip').style.visibility = "visible";
		EnterContent(TTitle, TContent);
		document.getElementById('ToolTip').innerHTML = ContentInfo;
	}else{
		tip_active = 0;
		document.getElementById('ToolTip').style.visibility = "hidden";
	}
}
