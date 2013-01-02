<?php
switch($_REQUEST[call]){
	case"":
	show();
	break;
	case"send_mssg":
	message();
	break;
}
function show(){
echo"<form name='mthanks' method='post' action='mobile.php'>";
echo"<input type='hidden' name='call' value='send_mssg'>";
echo"<input type='submit' value='Submit'>";
echo"</form>";
}
function do_post_request($url, $data, $optional_headers = null)
  {
     $params = array('http' => array(
                  'method' => 'POST',
                  'content' => $data
               ));
     if ($optional_headers !== null) {
        $params['http']['header'] = $optional_headers;
     }
     $ctx = stream_context_create($params);
     $fp = @fopen($url, 'rb', false, $ctx);
     if (!$fp) {
        echo"Problem with $url, $php_errormsg";
     }
     $response = @stream_get_contents($fp);
     if ($response === false) {
        echo"Problem reading data from $url, $php_errormsg";
     }
     return $response;
  }


function message(){
$url = 'http://web.textvertising.co.uk/cgi-bin/smssend.pl';  
$institution="Please reply TEXTSPARES to receive the information to your mobile as per the webiste.Shyam";
$uname="textspares";
$pass="o9fs168";
$rpt="xml";
$cust_phone="447771716012";
$fields = array(  
                        'numbers'=>urlencode($cust_phone),  
                        'user'=>urlencode($uname),  
                        'pass'=>urlencode($pass),  
                        'message'=>$institution,  
                        'smsid'=>82055,  
                        'expiry'=>1231  
                ); 
  
//url-ify the data for the POST  
foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }  
rtrim($fields_string,'&');  
//open connection  
$ch = curl_init();  
//set the url, number of POST vars, POST data  
curl_setopt($ch,CURLOPT_URL,$url);  
curl_setopt($ch,CURLOPT_POST,count($fields));  
curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch, CURLOPT_VERBOSE, 0);  
//execute post  
curl_exec($ch);  
//close connection  
curl_close($ch); 
}
?>


