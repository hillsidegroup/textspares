<?php
class Validator extends db {

/*"/^$/"*/

	var $checks = array();
	
		
	var $response = " style='color:#CC0000;'"; //HTML Error display
	var $form_error = false;
	var $duplicate_user = false;
	var $newuserchecks = true;
	
	function Validator()
	{
		$this->db(EZSQL_DB_USER, EZSQL_DB_PASSWORD, EZSQL_DB_NAME, EZSQL_DB_HOST);
	}
	
	function validate( $field, $type, $method = NULL , $match = NULL, $match_method = 0 )
	{
		
		if($_SERVER['REQUEST_METHOD']!="POST"){ return; }

		switch($match_method){
			case 0:
				//print("<p>".catch2."</p>");
				if($type=="text")
					{
					if(strlen($field)==0){
						if($method=="display"){ print $this->response; } else { $this->form_error = true; }
						}
					}
				elseif($type=="select")
					{
					if(eregi($this->checks[$type], $field) )
						{
						if($method=="display"){ print $this->response; } else { $this->form_error = true; }
						}
					}	
				elseif($type=="user" && $this->newuserchecks == true)
					{
					if(!empty($_GET['_load']) && $_GET['_load'] == "edituser")
					{ 
						$esql = "SELECT COUNT(*) FROM `supplier_company_users` WHERE strUsername = '".$this->mysql_prep($field)."' AND company_user_id !=".$this->mysql_prep($_GET['_userid']);
					} else {
						$esql = "SELECT COUNT(*) FROM `supplier_company_users` WHERE strUsername = '".$this->mysql_prep($field)."'";
					}
					
					$row_count = $this->get_var($esql);
					//$this->debug();
					if($row_count==1)
						{
						if($method=="display")
							{ print $this->response; $this->duplicate_user = true; } 
						else 
							{ $this->form_error = true; $this->duplicate_user = true; } 
						}
					else 
						{
						if( !preg_match($this->checks[$type], $field) )
							{
							if($method=="display")
								{ print $this->response; } 
							else 
								{ $this->form_error = true; }
							}
						}
					}
				elseif($type=="email"&&$this->newuserchecks==true)
				{
					//print(EMAIL);	
					if( (!empty($_GET['_load']) && $_GET['_load'] == "edituser") || (!empty($_GET['_subaction']) && $_GET['_subaction'] == "edit") ){ 
						//if( isset($_GET['_moduleid']) ){ $userID = $session->userdata['id']; } else { $userID = $_GET['_userid']; }
						$userID = $session->userdata['id'];
						$esql = "SELECT COUNT(*) FROM `supplier_company_users` WHERE strEmail = '".$this->mysql_prep($field)."' AND company_user_id !=".$this->mysql_prep($userID);
					} else {
						$esql = "SELECT COUNT(*) FROM `supplier_company_users` WHERE strEmail = '".$this->mysql_prep($field)."'";
					}
						
					$row_count = $this->get_var($esql);
					//$this->debug();
					$this->duplicate_email = false;
					if($row_count==1)
					{
						if($method=="display")
							{ print $this->response; $this->duplicate_email = true; }
						else 
							{ $this->form_error = true; $this->duplicate_email = true; }
					}
					elseif( !preg_match($this->checks[$type], $field) )
					{
						if($method=="display")
							{ print $this->response; print email_fail; } 
						else 
							{ $this->form_error = true; }
					}
				}
				else{
					
					if( !preg_match($this->checks[$type], $field) )
						{
						
						if($method=="display"){ print $this->response; 	} else { $this->form_error = true; }
						}
					}
					
				break;
			case 1:
				//print("Field:".$field."<br />");	
				//print( "<p>".$type."-".$match_method."</p>" );
				/*print("FIELD: ".$field.":<br>") ;
				print("if(!preg_match(".$this->checks[$type].",".$field."))<br>");	
				print("TEST:".preg_match($this->checks[$type], $field).":<br>");
				print("---------<br>");*/
				if( !preg_match($this->checks[$type], $field) || $field != $match )
					{
						
					if($method=="display"){ print $this->response;  } else { $this->form_error = true; }
					}
				
				break;
				}
		}	

	
}
?>