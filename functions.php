<?php

$_SESSION["BDDHOST"]="localhost";
$_SESSION["BDDNAME"]="villanono";
$_SESSION["BDDUSER"]="root";
$_SESSION["BDDPASS"]="";

function rqstprm($prm)
{
    $retrn=null;
    if(isset($_POST[$prm])){$retrn=$_POST[$prm];}
    elseif(isset($_GET[$prm])){$retrn=$_GET[$prm];}
    elseif(isset($_REQUEST[$prm])){$retrn=$_REQUEST[$prm];}
    return $retrn;
}
function bddmaj($sql, $is_sync=false)
{   
    $idaddSync="";
    if(!$is_sync){$sql=prepar_req($sql); $idaddSync=add_synchro($sql);}
    $pdo = getcon();
    $nb = $pdo->exec($sql);
    if($nb<=0 && $idaddSync!=""){del_synchro($idaddSync);}
    return $nb;
}
function bddfetch($sql)
{
    
    $pdo = getcon();
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(); 		
    return $result;
}
function bddprepare($sql)
{
    
    $pdo = getcon();
    $sth = $pdo->prepare($sql);
    $sth->execute();
    $result = $sth->fetchAll(); 		
    return $result;
}
function getcon()
{
    global $obj_pdo, $strEncodage;
    {$obj_pdo = new PDO("mysql:host=".$_SESSION["BDDHOST"].";dbname=".$_SESSION["BDDNAME"], $_SESSION["BDDUSER"], $_SESSION["BDDPASS"]);}
    //$obj_pdo -> exec("$strEncodage");
    return $obj_pdo;
}

function injsql($prm)
{
    $pdo = getcon();
    return $pdo->quote($prm);
}

function recursiveXML_Show($array, $counter=1 , $boolien1=true){
    $_Xmldetail="";$_Xmlinfos="";static $_id="";
    foreach( $array->children() as $key =>  $child){
        $txt =""; 
        echo '<br>';
        $txt .= separate($counter)." level ".$counter." |";

        if (!empty($child[0]->attributes())) {
            $langAttr = $child[0]->attributes()->lang;
            $txt .= "lang : ".$langAttr;
        }

        $txt .= " | [".$key .' : '. $child."]";
        
        $txt .= " | count:".count($child);
        echo $txt;
        if ($key == "id_bien") {
            $_id = $child;
        }
        if (count($child) > 0) {
            recursiveXML($child, $counter+1);
        }
        
        

        if ($boolien1) {
            $_Xmldetail = new SimpleXMLElement(requestXML(prepareURL('detail',$_id)));
            recursiveXML($_Xmldetail);
            //$_Xmlinfos = new SimpleXMLElement(requestXML(prepareURL('infos',$_id)));
            //recursiveXML($_Xmlinfos->sejours);
            $_Xmldetail = "";
            $_Xmlinfos = ""; 
        }


        
    } 
}
function get_corespandant_col($str_label="")
{
    //$str_column = "pr_id, nom_fr, nom_en, description_fr, description_en";
    //$partner_data_col = array('nom_bien','nom_bien_en','descriptif_bref','descriptif_bref_en'); 
    $tblcolumn["id_bien"]=array("pr_id", "");
    $tblcolumn["nom_bien"]=array("nom_fr", "");
    $tblcolumn["nom_bien_en"]=array("nom_en", "");
    $tblcolumn["descriptif_bref"]=array("description_fr", "");
    $tblcolumn["descriptif_bref_en"]=array("description_en", "");
    $tblcolumn["lbl_plus"]=array("nom_fr", "المزيد", "");
    $tblcolumn["lbl_plus"]=array("nom_fr", "المزيد", "");
    $tblcolumn["lbl_plus"]=array("nom_fr", "المزيد", "");
    $tblcolumn["lbl_plus"]=array("nom_fr", "المزيد", "");
    $tblcolumn["lbl_plus"]=array("nom_fr", "المزيد", "");
    $tblcolumn["lbl_plus"]=array("nom_fr", "المزيد", "");
    $tblcolumn["lbl_"]=array("", "", "");

    if(isset($tblcolumn[$str_label]))
    {
        if(true){return $tblcolumn[$str_label][0];} 
    }
    else{return $str_label;}
}


function main($partner_biens,$biens_result)
{
    global $global_query,$global_data,$global_counter,$mode,$chunks;
    $const= $global_counter;$current_query="";
    for ($i=$const; $i < count($partner_biens); $i++) {
        
        $value = $partner_biens[$i];
        
        $Xmldetail = new SimpleXMLElement(requestXML(prepareURL('detail',$value)));
        /* $Xmlinfos = new SimpleXMLElement(requestXML(prepareURL('infos',$value))); */
        recursiveXML($Xmldetail->detail,1);
        if (!findinsqlreqult($biens_result,$value)) {
            $current_query = build_sql("bien",$global_data,'insert') . "<br>";
        }else{
            $current_query = build_sql("bien",$global_data,'update') . "<br>";
        }
        
        if($mode == "progress") echo  $global_counter+1 . " / "  . count($partner_biens) . "<br>";
        if($mode == "sql") echo  $current_query . "<br>";
        $global_counter++;
        if ($i == $const+$chunks) {
            //echo "progress pass ".$i ;
            //$global_query = $current_query;
            //return $current_query;
            break;
        }else{
            
        }
    }
    
}
function findinsqlreqult($arr,$val)
{
    foreach ($arr as $key => $value) {
        if (in_array($val,$value)) {
            return true;
        }
    }
    return false;
}
function build_sql($table, $data , $type) {
    $query="";$str_values="";$str_id="";
    $str_column =  "";
    foreach ($data as $key => $value) {
        
        $str_val = injsql((string)$value);
        if ($type=="insert") {
            $str_column .= get_corespandant_col($key);
            $str_values .= $str_val;
            if ($key !== array_key_last($data)){
                $str_column .= ", ";
                $str_values .= ", ";
            }
        }else if ($type=="update") {
            if ($key != "id_bien") {
                $str_column .= get_corespandant_col($key) .'='.$str_val;
                if ($key !== array_key_last($data)){
                    $str_column .= ", ";
                    //$str_values .= ", ";
                }
            }else{
                $str_id = $str_val;
            }
        }
    }
    

    if ($type=="insert") {
        $query = "INSERT INTO {$table} ({$str_column}) VALUES ({$str_values});"; 
    }else if ($type=="update") {
        $query = "UPDATE {$table} SET {$str_column} WHERE pr_id = {$str_id};";
    }
    return $query;
}
    
function retrive_id_bien($array)
{
    $result=[];
    foreach( $array->children() as $key =>  $child){
        foreach( $child->children() as $key2 =>  $child2){
            if ($key2 == "id_bien") {
                //echo  $key .' : '. $child2;
                $result[] = (int) $child2;
            }
        }
    }
    return $result;
}
function prepareURL($action='biens', $_id_bien=""){
    
    $login = "xml@villanovo.com";
    $pass = "M4X876RV3N2D";
    $str_addons = "";
    if ($action == 'detail' || $action == 'infos') {
        $str_addons = "&id_bien=".$_id_bien ;
    }

    $url = "https://cimalpes.ski/fr/flux/?fonction=".$action."&login=".$login."&pass=".$pass."".$str_addons;
    return $url;
}

function separate($count){
    $txt = "";
    for ($i=0; $i < $count ; $i++) { 
        $txt .= "--";
    }
    return $txt;
}

function recursiveXML($array, $counter=1 , $boolien1=false){
    $_Xmldetail="";$_Xmlinfos="";static $_id="";
    global $mode;
    //information des biens
    $target_bien = array('id_bien','nom_bien','nom_bien_en','descriptif_bref','descriptif_bref_en');
    //equipement
    $target_equipment = array('equipement_demande','equipement_electromenager','equipement_general','node_equipement_multimedia','libelle');
    global $global_data;
    foreach( $array->children() as $key =>  $child){
        if ($key == "id_bien") {$_id = $child;}
               
        if($mode == "simplify")echo simplify($counter,$child,$key );
        //echo $txt;
        

        if (in_array($key,$target_bien)) {
           /*  $global_data[] = array($key => ; */
            $global_data[$key] = (string)$child;
        }
        if (in_array($key,$target_equipment)) {
            /*  $global_data[] = array($key => ; */
            if ($key == 'libelle') {
                $global_data[$key] = (string)$child;
            }else{

            }
             $global_data[$key] = (string)$child;
        }
        if (count($child) > 0) {
            recursiveXML($child, $counter+1);
        }
        
        //speacial affichage
        if (!empty($_id)&&$mode == "simplify") {
            $_Xmldetail = new SimpleXMLElement(requestXML(prepareURL('detail',$_id)));
            recursiveXML($_Xmldetail);
            //$_Xmlinfos = new SimpleXMLElement(requestXML(prepareURL('infos',$_id)));
            //recursiveXML($_Xmlinfos->sejours);
            $_Xmldetail = "";
            $_Xmlinfos = ""; 
        }
    } 
}

function gat_attr($child = null)
{
    $attrs = $child->attributes();$txt="";
    if (!empty($attrs)) {
        foreach ($attrs as $key => $attr) {
            $txt .= $key." : ".$attr;
        }
        return $txt;
    }
}

function simplify($counter,$value,$key )
{
    $val=$value;
    if (empty($value)) {
        $val = 'NONE';
    }
    $txt =""; 
    $txt .= '<br>' . separate($counter)." level ".$counter." |";
  
    $txt .= " | [".$key .' : '. $val."]";
    $txt .= " => count:".count($value);

    $txt .= " |ATTR| ". gat_attr($value);
    return $txt;
}

function requestXML($_url){
    $option = curl_init();
    curl_setopt($option, CURLOPT_URL,$_url);
    curl_setopt($option, CURLOPT_FAILONERROR,1);
    curl_setopt($option, CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($option, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($option, CURLOPT_TIMEOUT, 15);
    $retValue = curl_exec($option);          
    curl_close($option);
    return $retValue;
}