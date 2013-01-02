<?php 
ini_set("session.gc_maxlifetime", "600");
session_start(); 

if($_SERVER['QUERY_STRING']==""&&substr($_SERVER['REQUEST_URI'], strlen($_SERVER['REQUEST_URI'])-1)!="/"){ header("Location: ".$_SERVER['REQUEST_URI']."/"); }
?>