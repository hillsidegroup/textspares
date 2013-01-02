var xmlHttp

function show_all_Models(str,vall){
xmlHttp=GetXmlHttpObject();
if (xmlHttp==null)
  {
  alert ("Your browser does not support AJAX!");
  return;
  } 
  
var url="http://www.textspares.co.uk/model.php";
url=url+"?q="+str;
url=url+"&sid="+Math.random();
xmlHttp.onreadystatechange=getmodelsname;
//xmlHttp.onreadystatechange=getmodelsname
xmlHttp.open("GET",url,true);
xmlHttp.send(null);
}
function getmodelsname(){
	if (xmlHttp.readyState==4)
{ 
result=xmlHttp.responseText;
}
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
				i++;
			}
		}
	}
//}
	
}
	var arrname=new Array();
	var arrcat=new Array();
	var arrcnt=new Array();
	var arrcategory=new Array();
function show_Models(val1,val2){
	optval=val1.split("#");
	len=optval.length;
	//alert(len);
	len=len;
	for(i=0;i< len; i++){
        result= optval[i].split(",");		
        //alert(optval[i]);
        arrname[i]=result[0];
        arrcat[i]=result[1];
        arrcategory[i]=result[2];
        }
        lenar1=arrname.length;
        lenar2=arrcat.length-1;
        //alert("len1--->"+lenar1 + "len2--->" + lenar2);
        //alert(lenar);
        var obj=document.getElementById("modelid");
        len3=obj.length;
	while(len3>0)
	{
	len3=len3-1;
	obj.options[len3]=null; 		
	}
for(n=0;n< lenar2; n++){
	if(arrcat[n]== val2){
	len4=obj.length;
	optname=arrname[n];
	
	optname=optname.replace("_"," ");
	obj.options[len4]=new Option(optname,arrcategory[n]); 
	}
	
}        
	

}








function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}
