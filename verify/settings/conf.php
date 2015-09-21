<?php

$cms_ver="4.7.23b";
ini_set("display_errors", "On"); 
ini_set('session.bug_compat_warn', 0);
ini_set('session.bug_compat_42', 0);
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$session_id=session_id();
$_SESSION["s_id"]=0;
ini_set( 'magic_quotes_gpc', 'on');
$host_name=parse_url($_SERVER['HTTP_REFERER']);
$request_url2=$_SERVER['REQUEST_URI'];
//$request_url=(substr_count($request_url2,"?prin")>0)?current(explode("?print", $request_url2)):$request_url2;
$openwysiwyg_path="includes/openwysiwyg/";
$bbcode_path="includes/bbcode/";
$ass=$_SERVER['HTTP_HOST'];
$parrent_dir=$_SERVER['DOCUMENT_ROOT'];
$gallery_domen=$ass;
//$parrent_dir="";
/

$global_meil="ower@ower.com.ua";
$parrent_dir="";
$glb=array();
$glb["ftp_login"]="-";
$glb["ftp_password"]="-";
//$glb["db_host"]="127.0.0.1"; //укажите хост бд
//$glb["db_basename"]="makewear"; //укажите имя базы
//$glb["db_user"]="root"; // укажите имя пользователя бд
//$glb["db_password"]="123123q"; // укажите пароль бд
$glb["db_host"]="217.12.201.205"; //укажите хост бд
$glb["db_basename"]="zoond_make"; //укажите имя базы
$glb["db_user"]="zoond_make_r"; // укажите имя пользователя бд
$glb["db_password"]="makewear11"; // укажите пароль бд


$glb["session_id"]=$session_id;
$glb["teg_robots"]=false;
$glb["use_ftp"]=false;
$glb["use_internal_cats"]=true;
$glb["use_colors_and_sizes"]=false;
$glb["request_url"]=$request_url;
$glb["sys_mail"]=$global_meil;
$glb["mail_host"]=str_replace("www.","",$_SERVER['HTTP_HOST']);

$glb["request_url_encode"]=urldecode($request_url);
$glb["request_url"]=urldecode($request_url);
$glb["domain"]=$ass;
$cstatus[0]="-";
$cstatus[1]="Есть в наличии";
$cstatus[2]="Нет в наличии";
$cstatus[3]="Ожидается";
$glb["cstatus"]=$cstatus;


$cimgm[1]="Уменьшить";
$cimgm[2]="Уменьшить и обрезать";
$cimgm[3]="Уменьшить и залить свободную область";

