function chk_contact_form(frm){
var x=0;
if(frm.cname.value==""){
document.getElementById("error1").innerHTML="<span style='font-size:10px;color:red'>Enter Name</span>";
}
if(chkstring(frm.cname.value) == false){
document.getElementById("error1").innerHTML="<span style='font-size:10px;color:red'>Enter correct name</span>";
}else{
document.getElementById("error1").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if (validateEmail(frm.cemail.value)!=1){
document.getElementById("error2").innerHTML="<span style='font-size:10px;color:red'>Enter valid E-mail</span>";
}else{
document.getElementById("error2").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.telephone.value==""){
document.getElementById("error3").innerHTML="<span style='font-size:10px;color:red'>Enter your telephone</span>";
}else{
document.getElementById("error3").innerHTML="  <img src='images/round_icon.gif'>";

}
if(chkNumeric(frm.telephone.value) == false){
document.getElementById("error3").innerHTML="<span style='font-size:10px;color:red'>Enter valid telephone</span>";
}else{
document.getElementById("error3").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
var str=0;
if (frm.telephone.value.indexOf(0,0) != 0){
document.getElementById("error3").innerHTML="<span style='font-size:10px;color:red'>Telephone number must start with 0</span>";
}else{
document.getElementById("error3").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}		
if(frm.comments.value==""){
document.getElementById("error5").innerHTML="<span style='font-size:10px;color:red'> complete the comments</span>";
}
else{
document.getElementById("error5").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.security_code.value==""){
document.getElementById("error6").innerHTML="<span style='font-size:10px;color:red'> Enter the security code </span>";
}
else{
document.getElementById("error6").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(x==6)
return true;
else
return false;
}
function showimage(myID,myImg,Myaction) { //v3.0
document.getElementById(myID).src=myImg;
}
function changeimg(val){
document.getElementById("contimg").src=val;
}
function extract(what) {
if (what.indexOf('/') > -1)
answer = what.substring(what.lastIndexOf('/')+1,what.length);
else
answer = what.substring(what.lastIndexOf('\\')+1,what.length);
return answer;
}
function chk_validations(myID){
if(document.getElementById(myID).value<1){
document.getElementById("error1").innerHTML="<span style='font-size:10px;color:red'>Select Make</span>";
}else{
document.getElementById("error1").innerHTML="  <img src='images/round_icon.gif'>";
document.getElementById("error2").innerHTML="  <img src='images/round_icon.gif'>";
return true;
}
if(document.getElementById(myID).value==""){
document.getElementById("error3").innerHTML="<span style='font-size:10px;color:red'>Year of Registration</span>";
}else{
document.getElementById("error3").innerHTML="  <img src='images/round_icon.gif'>";
}
/*
if(frm.value<1){
document.getElementById("error1").innerHTML="<span style='font-size:10px;color:red'>Select Make</span>";
}else{
document.getElementById("error1").innerHTML="  <img src='images/round_icon.gif'>";
document.getElementById("error2").innerHTML="  <img src='images/round_icon.gif'>";
}
if(frm.value==""){
document.getElementById("error3").innerHTML="<span style='font-size:10px;color:red'>Select year</span>";
}else{
document.getElementById("error3").innerHTML="  <img src='images/round_icon.gif'>";
}
*/
}
function chk_partrequestfrm(frm){
var x=0;
if(frm.make_id.value<1){
document.getElementById("error1").innerHTML="<span style='font-size:10px;color:red'>Select the make</span>";
document.getElementById("error2").innerHTML="<span style='font-size:10px;color:red'>Select the model</span>";
//frm.make_id.focus();
}else{
document.getElementById("error1").innerHTML="  <img src='images/round_icon.gif'>";
document.getElementById("error2").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.reg_year.value==""){
document.getElementById("error3").innerHTML="<span style='font-size:10px;color:red'>Select a year</span>";
//frm.reg_year.focus();
}else{
document.getElementById("error3").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;	
}
if(frm.engine_capacity.value==""){
document.getElementById("error4").innerHTML="<span style='font-size:10px;color:red'>Select a engine</span>";
//frm.engine_capacity.focus();
}else{
document.getElementById("error4").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.fuel_type.value==""){
document.getElementById("error5").innerHTML="<span style='font-size:10px;color:red'>Select a fuel type</span>";
//frm.fuel_type.focus();
}else{
document.getElementById("error5").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.gear_type.value==""){
document.getElementById("error6").innerHTML="<span style='font-size:10px;color:red'>Select a transmission</span>";
//frm.gear_type.focus();
}else{
document.getElementById("error6").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.bodytype.value==""){
document.getElementById("error7").innerHTML="<span style='font-size:10px;color:red'>Select a body type</span>";
//frm.body_type.focus();
}else{
document.getElementById("error7").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.cust_name.value==""){
document.getElementById("error8").innerHTML="<span style='font-size:10px;color:red'>Enter a contact name</span>";
//frm.cust_name.focus();
}else{
document.getElementById("error8").innerHTML="  <img src='images/round_icon.gif'>";
flag8=true;
x=x+1;
}
if(chkstring(frm.cust_name.value) == false){
document.getElementById("error8").innerHTML="<span style='font-size:10px;color:red'>Enter correct name</span>";
//frm.cust_name.focus();
}else{
document.getElementById("error8").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(frm.cust_phone.value==""){
document.getElementById("error9").innerHTML="<span style='font-size:10px;color:red'>Enter your telephone</span>";
//frm.cust_phone.focus();
}else{
document.getElementById("error9").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
var str=0;
if (frm.cust_phone.value.indexOf(0,0) != 0){
document.getElementById("error9").innerHTML="<span style='font-size:10px;color:red'>Telephone number must start with 0</span>";
//frm.cust_phone.focus();
}else{
document.getElementById("error9").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
if(chkNumeric(frm.cust_phone.value) == false){
document.getElementById("error9").innerHTML="<span style='font-size:10px;color:red'>Enter valid telephone</span>";
//frm.cust_phone.focus();
}else{
document.getElementById("error9").innerHTML="  <img src='images/round_icon.gif'>";
x=x+1;
}
badStuff = ";:/,' \"\\";
for (i=0; i<badStuff.length; i++){
badCheck = badStuff.charAt(i)
if (frm.cust_phone.value.indexOf(badCheck,0) != -1){
document.getElementById("error9").innerHTML="<span style='font-size:10px;color:red'>No spaces please</span>";	
}
}
if(frm.cust_email.value!=""){
if (validateEmail(frm.cust_email.value)!=1){
document.getElementById("error10").innerHTML="<span style='font-size:10px;color:red'>Enter valid E-mail</span>";
//frm.cust_email.focus();	
x=x-1;
}else{
document.getElementById("error10").innerHTML="  <img src='images/round_icon.gif'>";
if(frm.confirm_email.value!=frm.cust_email.value){
if(document.getElementById("error11") != null) {
document.getElementById("error11").innerHTML="<span style='font-size:10px;color:red'>re-enter E-mail</span>";
}
//frm.confirm_email.focus();
x=x-1;	
}else{
if(document.getElementById("error11") != null) {
document.getElementById("error11").innerHTML = "  <img src='images/round_icon.gif'>";
}}}}
if(!(frm.agreechk.checked)){
alert("You must agree to the Terms & Conditions below to continue");
frm.cust_phone.focus();
return false;
}}
function chkNumeric(strString)
//  check for valid numeric strings	
{
var strValidChars = "0123456789.-";
var strChar;
var blnResult = true;
if (strString.length < 5) return false;
//  test strString consists of valid characters listed above
for (i = 0; i < strString.length && blnResult == true; i++)
{
strChar = strString.charAt(i);
if (strValidChars.indexOf(strChar) == -1)
{
blnResult = false;
}
}
return blnResult;
}
function chkstring(strString)
//  check for valid numeric strings	
{
var strValidChars = "0123456789.-";
var strChar;
var blnResult = true;
if (strString.length == 0) return false;
//  test strString consists of valid characters listed above
for (i = 0; i < strString.length && blnResult == true; i++)
{
strChar = strString.charAt(i);
if (strValidChars.indexOf(strChar)!= -1)
{
blnResult = false;
}
}
return blnResult;
}
function validateEmail(email)
{
// This function is used to validate a given e-mail 
// address for the proper syntax
if (email == ""){
return false;
}
badStuff = ";:/,' \"\\";
for (i=0; i<badStuff.length; i++){
badCheck = badStuff.charAt(i)
if (email.indexOf(badCheck,0) != -1){
return false;
}
}
posOfAtSign = email.indexOf("@",1)
if (posOfAtSign == -1){
return false;
}
if (email.indexOf("@",posOfAtSign+1) != -1){
return false;
}
posOfPeriod = email.indexOf(".", posOfAtSign)
if (posOfPeriod == -1){
return false;
}
if (posOfPeriod+2 > email.length){
return false;
}
return true
}
function show_all_Models(str,vall,surl){
//alert(window.href);
var knmxmlHttp=null;
try {
// Firefox, Opera 8.0+, Safari
knmxmlHttp=new XMLHttpRequest();
}
catch (e){
//Internet Explorer
try{
knmxmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
}
catch (e){
knmxmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
}
}
if (knmxmlHttp==null){
alert ("Browser does not support HTTP Request")
return
}
var url="http://www." + surl +"/" + "model.php";
url=url+"?q="+str;
url=url+"&sid="+Math.random();
knmxmlHttp.onreadystatechange = function() {
if (knmxmlHttp.readyState == 4) { // when request is complete
getmodelsname(knmxmlHttp.responseText,vall);
}
};
//xmlHttp.onreadystatechange=getmodelsname
knmxmlHttp.open("GET",url,true);
knmxmlHttp.send(null);
}
function getmodelsname(result,vall){
//alert(result);
result=result.split(",");
if(result==""){
var obj=document.getElementById("modelid");
var len;
len=obj.length;
while(len>0)
{
len=len-1;
obj.options[len]=null; 		
}
obj.options[0]=new Option( "---------------","-1"); 	
}else{
var obj=document.getElementById("modelid");
var len;
len=obj.length;
while(len>0)
{
len=len-1;
obj.options[len]=null; 		
}
for(i=0;i<result.length;i++)
{
len=obj.length;
result_array=result[i].split("=");
obj.options[len]=new Option( result_array[0],result_array[1]); 
} 
if(vall!= ""){
len=obj.length;
i=0;
while(len>i){
if(obj.options[i].value == vall){
obj.options[i].selected = true;
break;
}
i++;}}}
//}
}
function showModels(str,vall){
//alert(str + "----" + vall)
var nxmlHttp=null;
try {
// Firefox, Opera 8.0+, Safari
nxmlHttp=new XMLHttpRequest();
}
catch (e){
//Internet Explorer
try{
nxmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
}
catch (e){
nxmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
}
}
if (nxmlHttp==null){
alert ("Browser does not support HTTP Request")
return
}
var url="model.php";
url=url+"?q="+str;
url=url+"&sid="+Math.random();
nxmlHttp.onreadystatechange = function() {
if (nxmlHttp.readyState == 4) { // when request is complete
getcntryname(nxmlHttp.responseText,vall);
}
};
//xmlHttp.onreadystatechange=getstatesname
nxmlHttp.open("GET",url,true);
nxmlHttp.send(null);
}
function getcntryname(result,vall){
//alert(result);
result=result.split(",");
if(result==""){
var obj=document.getElementById("model_id");
var len;
len=obj.length;
while(len>0)
{
len=len-1;
obj.options[len]=null; 		
}
obj.options[0]=new Option( "---------------","-1"); 	
}else{
var obj=document.getElementById("model_id");
var len;
len=obj.length;
while(len>0)
{
len=len-1;
obj.options[len]=null; 		
}
for(i=0;i<result.length;i++)
{
len=obj.length;
result_array=result[i].split("=");
obj.options[len]=new Option( result_array[0],result_array[1]); 
} 
if(vall!= ""){
len=obj.length;
i=0;
while(len>i){
if(obj.options[i].value == vall){
obj.options[i].selected = true;
break;
}
i++;}}}
//}
}
function show_filter_Models(str,vall){
var nmxmlHttp=null;
try {
// Firefox, Opera 8.0+, Safari
fmxmlHttp=new XMLHttpRequest();
}
catch (e){
//Internet Explorer
try{
fmxmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
}
catch (e){
fmxmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
}}
if (fmxmlHttp==null){
alert ("Browser does not support HTTP Request")
return
}
var url="model.php";
url=url+"?q="+str;
url=url+"&sid="+Math.random();
fmxmlHttp.onreadystatechange = function() {
if (fmxmlHttp.readyState == 4) { // when request is complete
getfmodelsname(fmxmlHttp.responseText,vall);
}};
//xmlHttp.onreadystatechange=getmodelsname
fmxmlHttp.open("GET",url,true);
fmxmlHttp.send(null);
}
function getfmodelsname(result,vall){
//alert(result);
result=result.split(",");
if(result==""){
var obj=document.getElementById("fmodelid");
var len;
len=obj.length;
while(len>0)
{
len=len-1;
obj.options[len]=null; 		
}
obj.options[0]=new Option( "---------------","-1"); 	
}else{
var obj=document.getElementById("fmodelid");
var len;
len=obj.length;
while(len>0)
{
len=len-1;
obj.options[len]=null; 		
}
obj.options[0]=new Option( "select - model","-1"); 		
for(i=0;i<result.length;i++)
{
len=obj.length;
result_array=result[i].split("=");
obj.options[len]=new Option( result_array[0],result_array[1]); 
} 
if(vall!= ""){
len=obj.length;
i=0;
while(len>i){
//alert(obj.options[i].value);
//alert(vall);
if(obj.options[i].value == vall){
obj.options[i].selected = true;
break;
}
i++;}}}
//}
}