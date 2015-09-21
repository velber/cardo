<?php
/**
 * Created by PhpStorm.
 * User: volodini
 * Date: 7/2/15
 * Time: 12:21 PM
 */
session_start();
header('Content-Type: text/html; charset=utf-8');
$request_url=$_SERVER['REQUEST_URI'];
ini_set("max_execution_time", "99999");
set_time_limit(99999);
error_reporting(E_ALL^E_NOTICE);
require_once ('simple_html_dom.php');
//require_once ("../phpexcel/Classes/PHPExcel.php");
require_once ("gettover.php");
$_SESSION["cat_id"]=$_SESSION["cat_id"]?$_SESSION["cat_id"]:$cat_id;

function read_excel($filepath){
    $ar=array();
    $inputFileType = PHPExcel_IOFactory::identify($filepath);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($filepath);
    $ar = $objPHPExcel->getActiveSheet()->toArray();
    return $ar;
}

function ucfirst_mb($str) {
    $u='utf-8';
    return mb_substr(mb_strtoupper($str,$u),0,1,$u).mb_substr($str,1,strlen($str),$u);
}

function get_http_error($theurl) {
    $head=get_headers($theurl);
     if (!$head) {
        return false;
    } else {
        return substr($head[0],9,3);
    }
}
//-----Baner url FlFashion--------------
function flfashion($urll){
    $d=0;
    $fff=0;
    do{
        $a=get_http_error($urll);
        if($a=="404"){
            $urll=str_replace("plate-letnee/pla-202/","",$urll);
            $urll=str_replace("plate-letnee","platya",$urll);
            echo "Baner url2";
            $d++;
        }
        if($a=="200"){
            echo "work";
            $arru=explode("/",$urll);
            $sql=mysql_query("SELECT * FROM `shop_commodity`; ");
            while($u=mysql_fetch_assoc($sql)){
                $sql_pos=strpos($u['from_url'],$arru[count($arru)-1]);
                if($sql_pos!==false){
                    mysql_query("UPDATE `shop_commodity` SET `from_url`={$urll} WHERE `categoryID`='{$u['categoryID']}'; ");
                    echo " Update url";
                }
            }
            $fff=1;
        }
        if($d==3){
            echo "Not url!!!";
            $fff=1;
        }
    }while($fff==0);

    return $urll;
}
function setInterface($iddd, $count, $step, $result, $updated)
{
   $a=$count/100;
    $a2=round($step/$a, 2);
    $today = date("d-m-Y H:i:s");
    if ($step == 1) {
            $andSql .=', `update_add`=0';
    } elseif ($updated) {
        $andSql .=', `update_add`=`update_add`+1';
    }
    if($step == $count) {
            $result = "Complete! - ".$today;
         mysql_query("UPDATE `parser_interface` SET `check_prog`='{$step}',
          `update_prog`='100', `update_date`='{$today}', `text`='{$result}' WHERE `par_id`='{$iddd}' ");        
    } else {
         $result = mysql_real_escape_string($result);    
        mysql_query("UPDATE `parser_interface` SET `update_prog`='{$a2}', `check_prog`='{$step}', `text`='{$result}'{$andSql} WHERE `par_id`='{$iddd}' ");      
    }  
}

if ($price != "") {
$_SESSION['updateData'] = $updateData;    
$_SESSION['parser_id'] = $id;
    $_SESSION["cat_id"] = $cat_id;
    $_SESSION["h1"] = $h1;
    $_SESSION["price"] = $price;
    $_SESSION["price2"] = $price2;
    $_SESSION["desc"] = $desc;
    $_SESSION["sizeCol"] = $sizeCol;
    $_SESSION["cod"] = $cod;
    $_SESSION["links11"] = $links11;
    $_SESSION["per"] = $per;
    $_SESSION["no_nal"] = $nonal;    
    $date = new \DateTime('now');
    $day = $date->format('d_M');
    $dir_to_save = $day;
    if (!is_dir($dir_to_save)) {
        mkdir($dir_to_save);
    }
    $_SESSION['filename'] = $dir_to_save.DIRECTORY_SEPARATOR.'catid_'.$_SESSION['cat_id'].'.html';
    file_put_contents($_SESSION['filename'], '<meta charset="UTF-8">', FILE_APPEND); 

}
$count = count($_SESSION["links11"]);
if ($count > 0) 

{
    $links = $_SESSION["links11"];
    $step = $_GET["step"] ? $_GET["step"] : 1;
    $step2 = $step + 1;
    $count2 = $count - $step;
    $result = gettover($links[$step], $count2);
    setInterface($_SESSION['parser_id'], $count, $step, $result[0], $result[1]);
    $request_url = "verify.php?step={$step2}";
    if ($step == $count) {
        unset($_SESSION["links11"]);
        $id = $_SESSION['parser_id'];
        $request_url = "verify_start.php?id={$id}";
        echo "<script>setTimeout('ddddd();', 200);
					function ddddd()
					{
						location.href='{$request_url}';
					}
					</script>";
    } else {
        echo "<script>setTimeout('ddddd();', 700);
					function ddddd()
					{
						location.href='{$request_url}';
					}
					</script>";
    }
} else {
    echo "Нету ссилок!";
}

