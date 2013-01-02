function chk_quotes(frm){
element = frm.elements['chk[]']; 
var flag=0;
if(element.length){
for(x = 0; x < element.length;x++){ 
if(element[x].checked){
//var opval=element[x].value;
//var ele="price"+opval;
//alert(element[x].value);
if(document.getElementById("price" + element[x].value).value==""){
alert("Please enter price for item");
document.getElementById("price" + element[x].value).focus();
return false;
}
if(document.getElementById("grnt" + element[x].value).value==""){
alert("Please enter Guarantee for item");
document.getElementById("grnt" + element[x].value).focus();
return false;
}
if(document.getElementById("cndt" + element[x].value).value==""){
alert("Please enter Condition of Part");
document.getElementById("cndt" + element[x].value).focus();
return false;}}}}else{
if(element.checked){
if(document.getElementById("price" + element.value).value==""){
alert("Please enter price for item");
document.getElementById("price" + element.value).focus();
return false;
}
if(document.getElementById("grnt" + element.value).value==""){
alert("Please enter Guarantee for item");
document.getElementById("grnt" + element.value).focus();
return false;
}
if(document.getElementById("cndt" + element.value).value==""){
alert("Please enter Condition of Part");
document.getElementById("cndt" + element.value).focus();
return false;
}}}}
function togLayer(id) {
var e = document.getElementById(id);
if(e.style.display == 'none')
e.style.display = 'block';
else
e.style.display = 'none';
}
function MM_jumpMenu(targ,selObj,restore){ //v3.0
var myval=selObj.options[selObj.selectedIndex].value;
var optarray=myval.split(",");
str=new String(optarray[1]);
var mname=str.replace(" ","-");
eval(targ+".location='https://www.textspares.co.uk/MakeDetails/"+ optarray[0]+"/"+mname+"'");
if (restore) selObj.selectedIndex=0;
}
function showdropdown(a)
{
//document.getElementById(a).value;
}
var xmlHttp
var url;
var xmlHttp2
var url1;
var num2;
var objXmlHttp
var url4;
var num4;
var nul4;
var NewControlName="";
var CheckVal=0;
function GetXmlHttpObject(handler)
{ 
var objXmlHttp=null
if (navigator.userAgent.indexOf("Opera")>=0)
{
alert("This example doesn't work in Opera") 
return 
}
if (navigator.userAgent.indexOf("MSIE")>=0)
{ 
var strName="Msxml2.XMLHTTP"
if (navigator.appVersion.indexOf("MSIE 5.5")>=0)
{
strName="Microsoft.XMLHTTP"
} 
try
{ 
objXmlHttp=new ActiveXObject(strName)
objXmlHttp.onreadystatechange=handler 
return objXmlHttp
} 
catch(e)
{ 
alert("Error. Scripting for ActiveX might be disabled") 
return 
}}
if (navigator.userAgent.indexOf("Mozilla")>=0)
{
objXmlHttp=new XMLHttpRequest()
objXmlHttp.onload=handler
objXmlHttp.onerror=handler 
return objXmlHttp
}}
function showSub(cID,ControlName,val1)
{
	//alert(val1);
	var XMLhttpObj = false;
	if (typeof XMLHttpRequest != 'undefined'){
	XMLhttpObj = new XMLHttpRequest();
	} else if (window.ActiveXObject){
	try{
	XMLhttpObj = new ActiveXObject('Msxml2.XMLHTTP');
	} catch(e) {
	try{
	XMLhttpObj = new ActiveXObject('Microsoft.XMLHTTP');
	} catch(e) {}}}
	if (!XMLhttpObj) return;
	XMLhttpObj.onreadystatechange = function() {
	if (XMLhttpObj.readyState == 4) { // when request is complete
	FillControl(XMLhttpObj.responseText,ControlName,val1);
	}};
	url="ajax.php?sid="+ Math.random();
	XMLhttpObj.open('GET', url, true);
	XMLhttpObj.send(null);
}
function FillControl(results,controlname1,val1)
{
var results=results.split(",");
var len;
var obj=document.getElementById(controlname1);
len=obj.length;
while(len>0)
{
len=len-1;
obj.options[len]=null; 		
}
//obj.options[len]=new Option("---Select---"," "); 
for(i=0;i<results.length-1;i++)
{
len=obj.length;
result_array=results[i].split("=");
obj.options[len]=new Option( result_array[0],result_array[1]); 
}
if(val1!= ""){
len=obj.length;
i=0;
while(len>i){
if(obj.options[i].value == val1){
obj.options[i].selected = true;
break;
}
i++;}}}
function chk_supplierfrm1(frm,did){
flag=true;
if(frm.sup_company.value==""){
frm.sup_company.value=="";
//frm.sup_company.focus();
document.getElementById("error1").innerHTML="<span style='font-size:9px;color:red'>Enter Company name</span>";
flag=false;
//return false;
}
if(!isNaN(frm.sup_company.value)){
frm.sup_company.value=="";
//frm.sup_company.focus();
document.getElementById("error1").innerHTML="<span style='font-size:9px;color:red'>Enter correct  company name</span>";
return false;
}else{
document.getElementById("error1").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_name.value==""){
frm.sup_name.value="";
//frm.sup_name.focus();
document.getElementById("error2").innerHTML="<span style='font-size:9px;color:red'>Enter name</span>";
return false;
}
if(chkstring(frm.sup_name.value)== false){
//frm.sup_name.value="";
// frm.sup_name.focus();
document.getElementById("error2").innerHTML="<span style='font-size:9px;color:red'>Enter correct name</span>";
return false;
}else{
document.getElementById("error2").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_position.value==""){
frm.sup_position.value=="";
//frm.sup_position.select();
document.getElementById("error3").innerHTML="<span style='font-size:9px;color:red'>Enter Position</span>";
return false;
}
if(!isNaN(frm.sup_position.value)){
frm.sup_position.value=="";
//frm.sup_position.select();
document.getElementById("error3").innerHTML="<span style='font-size:9px;color:red'>Enter correct  Position</span>";
return false;
}else{
document.getElementById("error3").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_add1.value==""){
frm.sup_add1.value=="";
//frm.sup_add1.select();
document.getElementById("error4").innerHTML="<span style='font-size:9px;color:red'>Enter Address</span>";
return false;
}else{
document.getElementById("error4").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_county.value==""){
frm.sup_county.value=="";
//frm.sup_county.select();
document.getElementById("error5").innerHTML="<span style='font-size:9px;color:red'>Enter Town</span>";
return false;
}
if(chkstring(frm.sup_county.value)== false){
//frm.sup_name.value="";
//frm.sup_county.select();
document.getElementById("error5").innerHTML="<span style='font-size:9px;color:red'>Enter correct Town</span>";
return false;
}else{
document.getElementById("error5").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_zipcode.value==""){
frm.sup_zipcode.value=="";
//frm.sup_zipcode.select();
document.getElementById("error6").innerHTML="<span style='font-size:9px;color:red'>Enter Postal Code</span>";
return false;
}else{
document.getElementById("error6").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_email.value==""){
frm.sup_email.value=="";
//frm.sup_zipcode.select();
document.getElementById("error7").innerHTML="<span style='font-size:9px;color:red'>Enter E-mail</span>";
return false;
}
if (validateEmail(frm.sup_email.value)!=1){
document.getElementById("error7").innerHTML="<span style='font-size:9px;color:red'>Enter Valid E-mail</span>";
return (false);
}else{
document.getElementById("error7").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_phone.value==""){
frm.sup_phone.value=="";
//frm.sup_zipcode.select();
document.getElementById("error8").innerHTML="<span style='font-size:9px;color:red'>Enter Telephone</span>";
return false;
}
if(chkNumeric(frm.sup_phone.value) == false){
document.getElementById("error8").innerHTML="<span style='font-size:9px;color:red'>Enter Valid Telephone</span>";
return false;
}
var str=0;
if (frm.sup_phone.value.indexOf(0,0) != 0){
document.getElementById("error8").innerHTML="<span style='font-size:9px;color:red'>Number must start with 0</span>";
return false;
}else{
document.getElementById("error8").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_vat.value==""){
document.getElementById("error9").innerHTML="<span style='font-size:9px;color:red'>Vat Registered?</span>";
return false;
}else{
document.getElementById("error9").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_license.value==""){
document.getElementById("error10").innerHTML="<span style='font-size:9px;color:red'>Waste Management License?</span>";
return false;
}else{
document.getElementById("error10").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.sup_info.value==""){
document.getElementById("error11").innerHTML="<span style='font-size:9px;color:red'>Additional Info</span>";
return false;
}else{
document.getElementById("error11").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}
if(frm.security_code.value==""){
document.getElementById("error12").innerHTML="<span style='font-size:9px;color:red'>security code </span>";
return false;
}else{
document.getElementById("error12").innerHTML="  <img src='images/round_icon.gif'>";
//return true;
}}
function chk_supplierfrm(frm){
if(frm.sup_company.value==""){
alert("Please enter Company name");
frm.sup_company.focus();
return false;
}
if(!isNaN(frm.sup_company.value)){
alert("Please enter correct Company name");
frm.sup_company.focus();
return false;
}
if(frm.sup_name.value==""){
alert("Please enter Contact name");
frm.sup_name.focus();
return false;
}
if(chkstring(frm.sup_name.value) == false){
alert("Please enter correct contact name");
frm.sup_name.focus();
return false;
}
if(frm.sup_position.value==""){
alert("Please enter Position in company");
frm.sup_position.focus();
return false;
}
if(frm.sup_add1.value==""){
alert("Please enter address");
frm.sup_add1.focus();
return false;
}
if(frm.sup_county.value==""){
alert("Please enter Town/County name");
frm.sup_county.focus();
return false;
}
if(frm.sup_zipcode.value==""){
alert("Please enter Postal Code");
frm.sup_zipcode.focus();
return false;
}
if(frm.sup_email.value==""){
alert("Please enter E-mail address");
frm.sup_email.focus();
return false;
}
if (validateEmail(frm.sup_email.value)!=1)
{
alert("Please enter valid E-mail Address");
frm.sup_email.focus();	
return (false);
}
if(frm.sup_phone.value==""){
alert("Please enter Telephone number");
frm.sup_phone.focus();
return false;
}
if(chkNumeric(frm.sup_phone.value) == false){
alert("Please enter valid telephone number");
frm.sup_phone.focus();
return false;
}
var str=0;
if (frm.sup_phone.value.indexOf(0,0) != 0){
alert("Telephone number must start with 0");
frm.sup_phone.focus();
return false;
}
if(frm.sup_vat.value==""){
alert("VAT Registered?");
frm.sup_vat.focus();
return false;
}
if(frm.sup_license.value==""){
alert("Management License?");
frm.sup_license.focus();
return false;
}
if(frm.sup_info.value==""){
alert("Please complete the comments section so we may address your issue");
frm.sup_info.focus();
return false;
}
if(frm.security_code.value==""){
alert("Please enter Security code");
frm.security_code.focus();
return false;
}}
function chkNumeric(strString)
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
if (strValidChars.indexOf(strChar) == -1)
{
blnResult = false;
}}
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
}}
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
}}
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
