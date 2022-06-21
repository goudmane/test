<?php


include 'functions.php';
set_time_limit(0);
$mode = 'sql';
if (rqstprm('mode')) {
    $mode = rqstprm('mode');
}

$chunks = 9;
if (rqstprm('chunks')) {
    $chunks = intval(rqstprm('chunks'));
}




$global_data=[];
$global_query="";
$global_counter=0;
$XmlBiens = "";
$Xmldetail = "";
$Xmlinfos = "";

$count=0;
//recursiveXML($XmlBiens->biens ,1,true);
//get list id from xml 
$XmlBiens = new SimpleXMLElement(requestXML(prepareURL()));
$partner_biens =  retrive_id_bien($XmlBiens->biens );
//get data from database
$req = "SELECT id , pr_id FROM Bien";
$biens_result=bddfetch($req);

$display_mode = rqstprm("mode");
      
$count = count($partner_biens);


header( 'Content-type: text/html; charset=utf-8' );
do{
    
    $res = main($partner_biens,$biens_result);
    if ($global_counter >= 9) {
        break;
    }
}while($global_counter < $count)



?>