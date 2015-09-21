<?
/**
* OwerCMS
* Основные функции системы
* @package OwerCMS
* @author Ower
* @version 0.11.18
* @since engine v.0.10
* @link http://www.ower.com.ua
* @copyright (c) 2010+ by Ower
*/

 function removeDirectory($dir) {
    if ($objs = glob($dir."/*")) {
       foreach($objs as $obj) {
         is_dir($obj) ? removeDirectory($obj) : unlink($obj);
       }
    }
    rmdir($dir);
  }
  
function numberFormat($digit, $width) {
    while(strlen($digit) < $width)
      $digit = '0' . $digit;
      return $digit;
}

if(!function_exists("last_insert_id"))
{
	function last_insert_id()
	{
		return mysql_insert_id();
	}
}


function ftplogin($ftp_conn){
global $glb;
return ftp_login($ftp_conn,$glb["ftp_login"],$glb["ftp_password"]);
}

function countr()
{
	global $domen_ID,$glb;
	if($glb["counter_used"]!=1)
	{
		$r_date=date("Y-m-d");
		$time=date("H:i:s");
		$r_ip=$_SERVER['REMOTE_ADDR']!=$_SERVER['SERVER_ADDR']?$_SERVER['REMOTE_ADDR']:$_SERVER['HTTP_X_FORWARDED_FOR'];
		$brouser=$_SERVER['HTTP_USER_AGENT'];
		$session=session_id();
		$full_url=urldecode($_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
		$referrer=urldecode($_SERVER['HTTP_REFERER']);
	$q="INSERT INTO `counter` SET `domenID`='{$domen_ID}', `referrer`='{$referrer}', `date`='{$r_date}', `ip`='{$r_ip}', `brouser`='{$brouser}', `session`='{$session}', `atime`='{$time}', `full_url`='{$full_url}';";
	$pos = strpos($brouser, "bot");
	if ($pos === false) 
	{
		mysql_query($q);
	} 
	$glb["counter_used"]=1;
	}else
	{
		die();
	}
}

function fix_request_url($str)
{
if(substr_count($str,"?prin")>0)
	{
		$ret=current(explode("?print", $ret));
	}
	return $str;
}
function cleanstring($str)
{
	$str=ereg_replace("'","\'",$str);
	$str=ereg_replace("\r\n","",$str);
	$str=ereg_replace("\n","",$str);
	return $str;
}

function get_new_buttons2($name)
{
	global $request_url;
	$additions_buttons="
<td class='button' id='toolbar-new'>
<form action='{$request_url}' method='POST' id='id_new_adda'>
<input type='hidden' name='add_new' value='1'>

<div onclick=\"jQuery('#id_new_adda').submit();\" class='toolbar' style='width:100px!important;'> 
<span class='icon-32-new' title='Добавить'>
</span>
Добавить
</div>
</form>
</td>
";

return $additions_buttons;
}

function addslashes_for_array(&$arr) 
{
   foreach($arr as $k=>$v) 
   {
       if (is_array($v)) 
       {
           addslashes_for_array($v);
           $arr[$k] = $v;
       }
       else 
       {
           $v= addslashes($v);
           if($_SESSION["status"]!="admin")
           {
		$v = mysql_real_escape_string($v);
		$v = strip_tags($v);	
		$v = htmlspecialchars($v);
		$v = stripslashes($v);
		$v = ownsecure($v);
           }
            $arr[$k]=$v;
       }
   }
}
function ownsecure($text)
{
	$text=str_ireplace("DROP","",$text);
	$text=str_ireplace("FROM","",$text);
	$text=str_ireplace("SELECT","",$text);
	$text=str_ireplace("INSERT","",$text);
	$text=str_ireplace("WHERE","",$text);
	$text=str_ireplace("INNER","",$text);
	$text=str_ireplace("JOIN","",$text);
	$text=str_ireplace("DELETE","",$text);
	return $text;
}
function fix_magic_quotes_gpc() 
{
   if (!get_magic_quotes_gpc()) 
   {
       addslashes_for_array($_POST);
       addslashes_for_array($_GET);
       addslashes_for_array($_COOKIE);
   }
}
// -----------------------------------------------------------------------  СЕССИИ
function bd_session_start()
{
	ini_set('session.save_handler','user');
	session_set_save_handler( 'sess_open','sess_close','sess_read','sess_write','sess_destroy','sess_gb' );
	session_start();
}
// Эти функции оставим пустыми...
function sess_open($sess_path, $sess_name)
{
	return true;
}
function sess_close()
{
	return true;
}

// Читаем данные
function sess_read($sess_id)
{
	$sql="SELECT * FROM `system_sessions` WHERE `session_id` = '{$sess_id}'";
	$result=mysql_query( $sql );
	$current_time = date("Y-m-d H:i:s");
	if ( mysql_num_rows( $result ) > 0 )
	{
		$row = mysql_fetch_assoc( $result );
		$sql = "
		UPDATE
		`system_sessions`
		SET
		date_touched='{$current_time}', `user_ip`='{$_SERVER['REMOTE_ADDR']}'
		WHERE
		session_id='{$sess_id}'";
		mysql_query($sql) or die(mysql_error().'<br />'.$sql);
		// Как мы помним только из этого обработчика
		// Мы возвращаем данные, а не логическое значение:
		return html_entity_decode($row['sess_data']);
		}
	else
	{
		$sql = "INSERT INTO
		`system_sessions`
		SET
		session_id='{$sess_id}',
		`user_ip`='{$_SERVER['REMOTE_ADDR']}',
		date_touched='{$current_time}'";
		mysql_query($sql)or die(mysql_error().'<br />'.$sql);
		return '';
	}
}

// Пишем данные:
function sess_write($sess_id, $data)
{
	$current_time = date("Y-m-d H:i:s");
	$sql =" REPLACE `system_sessions` SET
	`date_touched`='{$current_time}', `sess_data`='".htmlentities($data,ENT_QUOTES,'UTF-8')."', `session_id`= '{$sess_id}', `user_ip`='{$_SERVER['REMOTE_ADDR']}'";
	mysql_query($sql)or die(mysql_error().'<br />'.$sql);
	return true;
}

// Уничтожаем данные:
function sess_destroy($sess_id)
{
	$sql='DELETE FROM `system_sessions`  WHERE `session_id`="'.$sess_id.'"';
	mysql_query($sql) or die(mysql_error().'<br />'.$sql);
	return true;
}

// Описываем действия сборщика мусора:
function sess_gb($sess_maxlifetime)
{
	$current_time = date("Y-m-d H:i:s");
	$sql="DELETE FROM `system_sessions` WHERE `date_touched` + '{$sess_maxlifetime}' < '{$current_time}'";
	mysql_query($sql) or die($sql);
	return true;
}
// -----------------------------------------------------------------------  //СЕССИИ
function antihacktext($input_text)
{
	$input_text = strip_tags($input_text);
	$input_text = htmlspecialchars($input_text);
	$input_text = mysql_escape_string($input_text);
	return $input_text;
}
function iflongload($time)
{
	$time2=microtime(1)-$_SERVER['REQUEST_TIME'];
	if($time2>1)
	{
		
		$long_date=date("Y-m-d H:i:s");
		$sql="
		INSERT INTO `system_longload`
		SET
		`long_date`='{$long_date}',
		`long_url`='{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}',
		`long_time`='{$time}',
		`long_time2`='{$time2}',
		`long_ip`='{$_SERVER['REMOTE_ADDR']}'
		;";
		mysql_query($sql);
		$time3=date("Y-m-d 23:23:59",strtotime(date("Y-m-d"))-60*60*24*30);
		$sql="
		DELETE FROM `system_longload`
		WHERE
		`long_date`<'{$time3}';";
		mysql_query($sql);
	}
}

function reg_text($string,$len=200)
{ 
	$string = str_replace("&nbsp;"," ",strip_tags($string));
	$string = implode(array_slice(explode('<br>',wordwrap($string,$len,'<br>',false)),0,1));
	return $string;
}
function get_true_price($price,$cur_id=0,$discount=0)
{
	global $glb,$curdd;
	$price=$discount>0?round($price-$price*$discount/100):$price;
	$price=$cur_id==0||$cur_id==""?round($glb["cur_val"]*$price,0):round($curdd[$cur_id]*$price*$glb["cur_val"],0);
	
	return $price;
}
function get_true_price2($price,$cur_id=0,$discount=0)
{
	global $glb,$curdd;
	$price=$discount>0?round($price-$price*$discount/100):$price;
	$price=$cur_id==0||$cur_id==""?round($glb["cur_val"]*$price,0):round($curdd[$cur_id]*$price*$glb["cur_val"],0);
	
	return $price;
}
function cur_function()
{
	global $cur_panel,$request_url,$cur_show,$glb,$curdd,$templates;
	$_SESSION['cur']=isset($_POST["cur"])?$_POST["cur"]:$_SESSION['cur'];


	$cur=isset($_SESSION['cur'])?$_SESSION['cur']:"UAH";
	$glb["templates"]->set_tpl('{$cur}', $cur);
	$cur_show=$cur;
	
	$query = "SELECT * FROM `shop_cur`;";
	$result=mysql_query($query) or die (mysql_error()); 
	if (mysql_num_rows($result) > 0)
	{
		$curses="";
		for($ij=1;$ij<=mysql_num_rows($result);$ij++)
		{
			$row = mysql_fetch_object($result);
			$cur_id=$row->cur_id;
			$cur_name=$row->cur_name;
			$full_name=$row->full_name;
			$$cur_name=$row->cur_val;
			$curdd[$cur_id]=$row->cur_val;
			if($cur_name==$cur)
			{
				$selected="selected='selected'";
				$cur_show=$row->cur_show;
				$glb["cur_val"]=$row->cur_val;
				$glb["cur_id"]=$cur_id;
				
			}else
			{
				$selected="";
			}
			$selected2=$cur_name==$cur?"checked":"";
			$curses=$curses."<option value='{$cur_name}' {$selected}>{$full_name}</option>";
			$langs_options2.="
			<li>
									<input type='radio' id='filter{$cur_id}' {$selected2} onchange='this.form.submit();' name='cur' value='{$cur_name}'>
									<label for='filter{$cur_id}'><span>{$cur_name}</span></label>
								</li>
			";	
			$cur_aviable[$cur_id]=$row->full_name;
			$glb["cur"][$cur_id]=$row->cur_show;
		}
	}
	$glb["cur_show"]=$cur_show;
	$glb["cur_aviable"]=$cur_aviable;
	$glb["templates"]->set_tpl('{$cur_val}', $glb["cur_val"]);
	$glb["templates"]->set_tpl('{$cur_show}', $cur_show);
	$cur_panel="
	<form method='post' action='$request_url' class='checkboxes'>
							<ul>
		{$langs_options2}
	</ul>
	</form>
	";
	$templates->set_tpl('{$cur_panel}',$cur_panel);
}
function sape_text()
{
	if (!defined('_SAPE_USER')){ define('_SAPE_USER', 'fac51abf89cc0eb96f3f00395022555b');  }
	require_once(_SAPE_USER.'/sape.php'); 
	$o['multi_site'] = true; 
	$sape = new SAPE_client($o);
	$sape_text=$sape->return_links(10);
	return $sape_text;
}
if(false)
{
function sape_text()
{
	if (!defined('_SAPE_USER')){ define('_SAPE_USER', 'fac51abf89cc0eb96f3f00395022555b');  }
	require_once(_SAPE_USER.'/sape.php');
	$o['multi_site'] = true;
	$sape = new SAPE_client($o);
	$sape_text=$sape->return_links(10);
	return $sape_text;
}
}

function creatfolder($url)
{
		if($glb["use_ftp"])
		{
			$ftp_conn=ftp_connect($gallery_domen);
			$ftp_log=ftplogin($ftp_conn);
		}
		if(is_dir($url)==false)
		{
			if($glb["use_ftp"])
			{
				ftp_mkdir($ftp_conn,$parrent_dir."/".$url);
			}else
			{
				mkdir($url);
			}
		}
		$_SESSION["upload_dir"]=$url;
		return true;
}

function getnewimg($type=1,$x,$y,$prefix,$item_id,$new_file_name,$wm=0,$url="")
{
/**
* getnewimg
* обрабатывает принимаеммое методом POST исображение и редактирует его
* @version 3.6.6
* @param integer $type - тип обработки, 1 - вписывает в область, 2 - обрезает к области, 3 - фиксированный размер
* @return boolean
*/
	global $parrent_dir,$gallery_domen,$glb;
	
	
	
	$wm=($glb["watermark"]==0)?0:$wm;
	$dest="uploads/temp_image.jpg";
	$src="images/{$prefix}/{$item_id}/".$new_file_name;
	$path = "/".$src;
	if(isset($_FILES["myfile1"])||$url!="")
	{
		if($glb["use_ftp"])
		{
			$ftp_conn=ftp_connect($gallery_domen);
			$ftp_log=ftplogin($ftp_conn);
		}
		if(is_dir("images/{$prefix}/{$item_id}")==false)
		{
			if($glb["use_ftp"])
			{
				ftp_mkdir($ftp_conn,$parrent_dir."/images/{$prefix}/{$item_id}");
			}else
			{
				mkdir("images/{$prefix}/{$item_id}");
			}
		}
		if($url=="")
		{
			$myfile = $_FILES["myfile1"]["tmp_name"];
			$myfile_name = $_FILES["myfile1"]["name"];
			$myfile_size = $_FILES["myfile1"]["size"];
			$myfile_type = $_FILES["myfile1"]["type"];
			$error_flag = $_FILES["myfile1"]["error"];
		}else
		{
			$myfile = $url;
			$error_flag=0;
		}

		ini_set('display_errors',1);
		error_reporting(E_ALL ^E_NOTICE);

		
		if($error_flag == 0)
		{

			if($glb["use_ftp"])
			{
				ftp_put($ftp_conn,$parrent_dir."/".$dest, $myfile, FTP_BINARY);
			}elseif($url!="")
			{
				copy($myfile,$dest);
			}else
			{
				move_uploaded_file($myfile,$dest);
			}
			$an_sp=$path;
			$size = getimagesize($dest);
			if ($size === false)
			{
				$ret=false;
			}else
			{
				$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
				$icfunc = "imagecreatefrom{$format}";
				if (function_exists($icfunc))
				{
					if($type==1)
					{
						$new_img_x=min($x,$size[0]);
						$new_img_y=min($y,$size[1]);
						$x_ratio = $new_img_x / $size[0];
						$y_ratio = $new_img_y / $size[1];
						if($y_ratio<$x_ratio)
						{
							$x_ratio = $y_ratio;
							$new_img_x=$size[0]*$x_ratio;
						}else
						{
							$y_ratio = $x_ratio;
							$new_img_y=$size[1]*$y_ratio;
						}
						$ratio = min($x_ratio, $y_ratio);
					}elseif($type==2)
					{
						$new_img_x=min($x,$size[0]);
						$new_img_y=min($y,$size[1]);
						$x_ratio = $new_img_x / $size[0];
						$y_ratio = $new_img_y / $size[1];
						$ratio = max($x_ratio, $y_ratio);
					}elseif($type==3)
					{
						$new_img_x=$x;
						$new_img_y=$y;
						$x_ratio = $new_img_x / $size[0];
						$y_ratio = $new_img_y / $size[1];
						$ratio = min($x_ratio, $y_ratio);
					}
					$use_x_ratio = ($x_ratio == $ratio);
					$new_width   = $use_x_ratio  ? $new_img_x  : floor($size[0] * $ratio);
					$new_height  = !$use_x_ratio ? $new_img_y : floor($size[1] * $ratio);
					$new_left    = $use_x_ratio  ? 0 : floor(($new_img_x - $new_width) / 2);
					$new_top     = !$use_x_ratio ? 0 : floor(($new_img_y - $new_height) / 2);
					$isrc = $icfunc($dest);
					$idest = imagecreatetruecolor($new_img_x, $new_img_y);
					if($format=="png")
					{
					imageAlphaBlending($idest, false);
					imageSaveAlpha($idest, true);
					}
					$rgb=imagecolorallocate($idest,255,255,255);
					imagefill($idest, 0, 0, $rgb);
					imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
					$funcimg="image{$format}";
					$wdwd=$format=="png"?0:90;
					$funcimg($idest, $dest,$wdwd);
					if($glb["use_ftp"])
					{
						ftp_put($ftp_conn,$parrent_dir."/".$src, $dest, FTP_BINARY);
					}else
					{
						copy($dest,$src);
					}
					imagedestroy($isrc);
					imagedestroy($idest);
					if($wm==1&&$glb["watermark"])
					{
						$watermark = new watermark3();
						$img = $icfunc($src);
						$water = imagecreatefrompng("images/watermark1.png");
						$im=$watermark->create_watermark($img,$water,70,$wm);
						imagejpeg($im,$src,100);

					}
					$ret=true;
				}else
				{
					$ret=false;
				}
			}
		}else
		{
			$ret=false;
		}
	}else
	{
		$ret=false;
	}
	return $ret;
}

function getnewimg_uploadify($type=1,$x,$y,$prefix,$item_id,$new_file_name,$wm=0)
{
/**
* getnewimg_uploadify
* обрабатывает принимаеммое методом POST исображение и редактирует его
* @version 3.6.6
* @param integer $type - тип обработки, 1 - вписывает в область, 2 - обрезает к области, 3 - фиксированный размер
* @return boolean
*/
	global $parrent_dir,$gallery_domen,$glb,$ftp_conn;
	$dest="uploads/temp_image.jpg";
	$src="images/{$prefix}/{$item_id}/".$new_file_name;
	$path = "/".$src;
	if(isset($_FILES["Filedata"]))
	{
		if($glb["use_ftp"])
		{
			$ftp_conn=ftp_connect($gallery_domen);
			$ftp_log=ftplogin($ftp_conn);
		}
		if(is_dir($_SERVER['DOCUMENT_ROOT']."/images/{$prefix}/{$item_id}")==false)
		{
			if($glb["use_ftp"])
			{
				ftp_mkdir($ftp_conn,$parrent_dir."/images/{$prefix}/{$item_id}");
			}else
			{
				//die("images/{$prefix}/");
				mkdir($_SERVER['DOCUMENT_ROOT']."/images/{$prefix}/{$item_id}");
				
			}
		}
		$myfile = $_FILES["Filedata"]["tmp_name"];
		$myfile_name = $_FILES["Filedata"]["name"];
		$myfile_size = $_FILES["Filedata"]["size"];
		$myfile_type = $_FILES["Filedata"]["type"];
		$error_flag = $_FILES["Filedata"]["error"];


	//	ini_set('display_errors',1);
//error_reporting(E_ALL ^E_NOTICE);

		
		if($error_flag == 0)
		{

			if($glb["use_ftp"])
			{
				ftp_put($ftp_conn,$parrent_dir."/".$dest, $myfile, FTP_BINARY);
			}else
			{
				move_uploaded_file($myfile,$_SERVER['DOCUMENT_ROOT'].'/'.$dest);
			}
			$an_sp=$path;
			$size = getimagesize($_SERVER['DOCUMENT_ROOT'].'/'.$dest);
			if ($size === false)
			{
				$ret=false;
			}else
			{
				$format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
				$icfunc = "imagecreatefrom{$format}";
				if (function_exists($icfunc))
				{
					if($type==1)
					{
						$new_img_x=min($x,$size[0]);
						$new_img_y=min($y,$size[1]);
						$x_ratio = $new_img_x / $size[0];
						$y_ratio = $new_img_y / $size[1];
						if($y_ratio<$x_ratio)
						{
							$x_ratio = $y_ratio;
							$new_img_x=$size[0]*$x_ratio;
						}else
						{
							$y_ratio = $x_ratio;
							$new_img_y=$size[1]*$y_ratio;
						}
						$ratio = min($x_ratio, $y_ratio);
					}elseif($type==2)
					{
						$new_img_x=min($x,$size[0]);
						$new_img_y=min($y,$size[1]);
						$x_ratio = $new_img_x / $size[0];
						$y_ratio = $new_img_y / $size[1];
						$ratio = max($x_ratio, $y_ratio);
					}elseif($type==3)
					{
						$new_img_x=$x;
						$new_img_y=$y;
						$x_ratio = $new_img_x / $size[0];
						$y_ratio = $new_img_y / $size[1];
						$ratio = min($x_ratio, $y_ratio);
					}
					$use_x_ratio = ($x_ratio == $ratio);
					$new_width   = $use_x_ratio  ? $new_img_x  : floor($size[0] * $ratio);
					$new_height  = !$use_x_ratio ? $new_img_y : floor($size[1] * $ratio);
					$new_left    = $use_x_ratio  ? 0 : floor(($new_img_x - $new_width) / 2);
					$new_top     = !$use_x_ratio ? 0 : floor(($new_img_y - $new_height) / 2);
					$isrc = $icfunc($_SERVER['DOCUMENT_ROOT'].'/'.$dest);
					$idest = imagecreatetruecolor($new_img_x, $new_img_y);
					if($format=="png")
					{
					imageAlphaBlending($idest, false);
					imageSaveAlpha($idest, true);
					}
					$rgb=imagecolorallocate($idest,255,255,255);
					imagefill($idest, 0, 0, $rgb);
					imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $size[0], $size[1]);
					$funcimg="image{$format}";
					$wdwd=$format=="png"?0:90;
					$funcimg($idest, $_SERVER['DOCUMENT_ROOT'].'/'.$dest,$wdwd);
					if($glb["use_ftp"])
					{
						ftp_put($ftp_conn,$parrent_dir."/".$src, $_SERVER['DOCUMENT_ROOT'].'/'.$dest, FTP_BINARY);
					}else
					{
						copy($_SERVER['DOCUMENT_ROOT'].'/'.$dest,$_SERVER['DOCUMENT_ROOT'].'/'.$src);
					}
					imagedestroy($isrc);
					imagedestroy($idest);
					if($wm==1&&$glb["watermark"])
					{
						$watermark = new watermark3();
						$img = $icfunc($_SERVER['DOCUMENT_ROOT'].'/'.$src);
						$water = imagecreatefrompng($_SERVER['DOCUMENT_ROOT'].'/'."images/watermark1.png");
						$im=$watermark->create_watermark($img,$water,70,$wm);
						imagejpeg($im,$_SERVER['DOCUMENT_ROOT'].'/'.$src,100);
						
					

					}
					$ret=true;
				}else
				{
					$ret=false;
				}
			}
		}else
		{
			$ret=false;
		}
	}else
	{
		$ret=false;
	}
	return $ret;
}

function delete_img($dir,$item_id,$file_name){
	global $glb,$parrent_dir,$gallery_domen;
	$src = $item_id===NULL?"images/{$dir}/{$file_name}":"images/{$dir}/{$item_id}/{$file_name}";
	if(file_exists($src)){
		if($glb['use_ftp']){
			$ftp_conn=ftp_connect($gallery_domen);
			$ftp_log=ftplogin($ftp_conn);
			$result = ftp_delete($ftp_conn,$parrent_dir.'/'.$src);
		}else{
			$result = unlink($_SERVER['DOCUMENT_ROOT'].'/'.$src);
		}
	}
	return $result;
}



function sql_line_tpl($sql,$tplfile)
{
	global $glb;
	$res=mysql_query($sql);
	while($row=mysql_fetch_assoc($res))
	{
		$countries.="<option value='{$row['country_id']}'>{$row['country_name_ru']}</option>";
		if(count($row))
		foreach($row as $key=>$value)
		{
			$glb["templates"]->set_tpl('{$'.$key.'}', $value);
		}
		$all_lines.=$glb["templates"]->get_tpl($tplfile);
	}
	return $all_lines;
}

class modul {
// данные (свойства):
var $name, $name1,$name2,$name3,$name4, $enabled;

}

class templ
{
	var $vars=array();
	var $template=array();

	function get_tpl_file($tpl_name,$pref="")
	{
		global $theme_name;
		$pos = strpos($tpl_name, "modules");
		if ($pos === false) {
			$file_name="templates/{$theme_name}/{$tpl_name}.tpl";
		} else {
			$file_name="{$tpl_name}.tpl";
		}

		
		if((empty($file_name)) || (!file_exists($pref.$file_name)))
		{
			return false;
		}else
		{
			$this->template[$tpl_name]=file_get_contents($pref.$file_name);
			//$this->template[$tpl_name]=str_replace("\$", "", $this->template[$tpl_name]);
		}
	}

	function get_tpl($tpl_name,$pref="")
	{
		if($this->template[$tpl_name]=="")
		{
			$this->get_tpl_file($tpl_name,$pref);
		}
		$asd=$this->tpl_parse($tpl_name);
		return $asd;
	}
	function get_tpl_main($tpl_name)
	{
		global $theme_name;
		$file_name="templates/{$theme_name}/{$tpl_name}.tpl";
		$temp=file_get_contents($file_name);
		$match_expression = '/\{\$([0-9a-z_^\.\-]+)}/';
		preg_match_all($match_expression,$temp,$matches);
		for ($i=0; $i< count($matches[0]); $i++) 
		{
			$real_name=$matches[1][$i];
			global $$real_name;
			$temp=str_replace('{$'.$matches[1][$i].'}', $$real_name,$temp);
		}
		$match_expression = '/\{\$([0-9a-z_^\.\-]+)\[([0-9]+)\]}/';
		preg_match_all($match_expression,$temp,$matches);
		for ($i=0; $i< count($matches[0]); $i++) 
		{
			$real_name=$matches[1][$i];
			$real_name2=$matches[2][$i];
			global $$real_name["$real_name2"];$aa=$$real_name[$real_name2];die($aa);
			$temp=str_replace('{$'.$real_name.'['.$real_name2.']'.'}', $$real_name[$real_name2],$temp);
		}
		return $temp;
	}
	function set_tpl($key,$var)
	{
		$this->vars[$key] = $var;
	}
	function unset_tpl()
	{
		foreach($this->vars as $find => $replace)
		{
			$this->vars[$find]="";
		}
	}
	function tpl_parse($tpl_name)
	{
		$asd=$this->template[$tpl_name];
		foreach($this->vars as $find => $replace)
		{
			$asd = str_replace($find, $replace,$asd );
		}
		return $asd;
	}
}


function admin_names($name)
{
	global $$name,$modul_name,$mod_name1,$mod_name2,$mod_name3,$mod_name4,$mod_name5;
	$modul_name=$name;
	$mod_name1=$$modul_name->name1;
	$mod_name2=$$modul_name->name2;
	$mod_name3=$$modul_name->name3;
	$mod_name4=$$modul_name->name4;
	return 1;
}
function translit($st)
{

$st=strtr($st,"абвгдеёзийклмнопрстуфхъыэ","abvgdeeziyklmnoprstufh'ie"); // <----- строчные
$st=strtr($st,"АБВГДЕЁЗИЙКЛМНОПРСТУФХЪЫЭ","ABVGDEEZIYKLMNOPRSTUFH'IE"); // <----- ПРОПИСНЫЕ
$st=strtr($st, array( 
"ж"=>"zh", 
"ц"=>"ts", 
"ч"=>"ch", 
"ш"=>"sh", 
"щ"=>"shch",
"ь"=>"", 
"ю"=>"yu", 
"я"=>"ya", 
"Ж"=>"ZH", 
"Ц"=>"TS", 
"Ч"=>"CH", 
"Ш"=>"SH", 
"Щ"=>"SHCH",
"Ь"=>"", 
"Ю"=>"YU", 
"Я"=>"YA", 
"ї"=>"i", 
"Ї"=>"Yi", 
"є"=>"ye", 
"Є"=>"Ye")

);



$st=strtr($st, array( 

":"=>"",
"«"=>"",
"»"=>"",
"\""=>"",
"'"=>"",
"?"=>"",
" "=>"-" )

);
return $st;
}


function fix_replace($text)
{
	$text=str_replace(chr(10), "",$text);  
	$text=str_replace(chr(13), "",$text);
	$text=str_replace("'", "\'", $text); 
	return $text;
}


function get_edit_buttons($delete_url)
{
	$additions_buttons="
<td class='button' id='toolbar-save'>
<div onclick='javascript:func_save();' class='toolbar'>
<span class='icon-32-save' title='Сохранить'>
</span>
Сохранить
</div>
</td>

<td class='button' id='toolbar-apply'>
<div onclick='javascript:func_apply();' class='toolbar'>
<span class='icon-32-apply' title='Применить'>
</span>
Применить
</div>
</td>

<td class='button' id='toolbar-cancel'>
<div onclick='javascript:gotospec(1);' class='toolbar'>
<span class='icon-32-cancel' title='Отменить'>&nbsp;
</span>
Отменить
</div>
</td>

<td class='button' id='toolbar-delete'>
<div onclick='javascript:go_to_page(\"{$delete_url}\");' class='toolbar'>
<span class='icon-32-delete' title='Удалить'>&nbsp;
</span>
Удалить
</div>
</td>
";

return $additions_buttons;
}
function get_edit_buttons2($delete_url)
{
	$additions_buttons="
<td class='button' id='toolbar-cancel'>
<div onclick='javascript:gotospec(1);' class='toolbar'>
<span class='icon-32-cancel' title='Отменить'>&nbsp;
</span>
Отменить
</div>
</td>

<td class='button' id='toolbar-delete'>
<div onclick='javascript:go_to_page(\"{$delete_url}\");' class='toolbar'>
<span class='icon-32-delete' title='Удалить'>&nbsp;
</span>
Удалить
</div>
</td>
";

return $additions_buttons;
}
function get_new_buttons($add_url,$name)
{
	$additions_buttons="
<td class='button' id='toolbar-new'>
<div onclick='javascript:go_to_page(\"{$add_url}\");' class='toolbar' style='width:100px!important;'> 
<span class='icon-32-new' title='{$name}'>
</span>
{$name}
</div>
</td>
";

return $additions_buttons;
}

function get_new_buttons234($name)
{
	global $request_url;
	$additions_buttons="
<td class='button' id='toolbar-new'>
<form action='{$request_url}' method='POST' id='id_new_adda'>
<input type='hidden' name='add_new' value='1'>
<input type='hidden' name='par1' value='0' id='par1'>
<input type='hidden' name='par2' value='0' id='par2'>
<input type='hidden' name='par3' value='0' id='par3'>
<div onclick=\"jQuery('#id_new_adda').submit();\" class='toolbar' style='width:100px!important;'> 
<span class='icon-32-new' title='{$name}'>
</span>
{$name}
</div>
</form>
</td>
";

return $additions_buttons;
}

function get_add_buttons()
{
	$additions_buttons="
<td class='button' id='toolbar-save'>
<div onclick='javascript:func_save();' class='toolbar'>
<span class='icon-32-save' title='Сохранить'>
</span>
Сохранить
</div>
</td>

<td class='button' id='toolbar-cancel'>
<div onclick='javascript:gotospec(1);' class='toolbar'>
<span class='icon-32-cancel' title='Отменить'>&nbsp;
</span>
Отменить
</div>
</td>
";

return $additions_buttons;
}

function get_add_buttons22()
{
	$additions_buttons="
<td class='button' id='toolbar-save'>
<div onclick='javascript:func_save();' class='toolbar'>
<span class='icon-32-save' title='Добавить'>
</span>
Добавить
</div>
</td>

<td class='button' id='toolbar-cancel'>
<div onclick='javascript:gotospec(1);' class='toolbar'>
<span class='icon-32-cancel' title='Отменить'>&nbsp;
</span>
Отменить
</div>
</td>
";

return $additions_buttons;
}

function send_mime_mail($name_from, // имя отправителя
                        $email_from, // email отправителя
                        $name_to, // имя получателя
                        $email_to, // email получателя
                        $data_charset, // кодировка переданных данных
                        $send_charset, // кодировка письма
                        $subject, // тема письма
                        $body // текст письма
                        ) {
$data_charse="windows-1251";
$send_charset="windows-1251";
  $to = mime_header_encode($name_to, $data_charset, $send_charset)
                 . ' <' . $email_to . '>';
  $subject = mime_header_encode($subject, $data_charset, $send_charset);
  $from =  mime_header_encode($name_from, $data_charset, $send_charset)
                     .' <' . $email_from . '>';
  if($data_charset != $send_charset) {
    $body = iconv($data_charset, $send_charset."//IGNORE", $body);
  }
  $headers = "From: $from\r\n";
  $headers .= "Content-type: text/plain; charset=$send_charset\r\n";

  return mail($to, $subject, $body, $headers);
}


function send_mime_mail_html($name_from, // имя отправителя
                        $email_from, // email отправителя
                        $name_to, // имя получателя
                        $email_to, // email получателя
                        $data_charset, // кодировка переданных данных
                        $send_charset, // кодировка письма
                        $subject, // тема письма
                        $body // текст письма
                        ) {
$data_charse="windows-1251";
$send_charset="windows-1251";
  $to = mime_header_encode($name_to, $data_charset, $send_charset)
                 . ' <' . $email_to . '>';
  $subject = mime_header_encode($subject, $data_charset, $send_charset);
  $from =  mime_header_encode($name_from, $data_charset, $send_charset)
                     .' <' . $email_from . '>';
  if($data_charset != $send_charset) {
    $body = iconv($data_charset, $send_charset."//IGNORE", $body);
  }
  $headers = "From: $from\r\n";
  $headers .= "Content-type: text/html; charset=$send_charset\r\n";

  return mail($to, $subject, $body, $headers);
}

function mime_header_encode($str, $data_charset, $send_charset) {
  if($data_charset != $send_charset) {
    $str = iconv($data_charset, $send_charset, $str);
  }
  return '=?' . $send_charset . '?B?' . base64_encode($str) . '?=';
}

function newsize($new_width,$new_height,$watermark_img_obj_w,$watermark_img_obj_h)
{
//echo $new_width,"<br>",$new_height,"<br>",$watermark_img_obj_w,"<br>",$watermark_img_obj_h;die();
	$new_left=0;
	$new_top=0;
	$src="images/watermark1.png";
	$dest="uploads/temp_image2.jpg";
	$format="png";
	$icfunc = "imagecreatefrom{$format}";
	$isrc = $icfunc($_SERVER['DOCUMENT_ROOT'].'/'.$src);
	$idest = imagecreatetruecolor($new_width, $new_height);
	if($format=="png")
	{
		imageAlphaBlending($idest, false);
		imageSaveAlpha($idest, true);
	}
	
	imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, $new_width, $new_height, $watermark_img_obj_w,$watermark_img_obj_h);
	$funcimg="image{$format}";
	$wdwd=$format=="png"?0:100;
	$funcimg($idest, $_SERVER['DOCUMENT_ROOT'].'/'.$dest,$wdwd);
	
	return $idest;
}
function get_admin_content_tree()
{
	global $templates,$modules;
	foreach ($modules as $key => $value)
	{
		if($value==1)
		{
			$icfunc = "get_tree_".$key;
			if (function_exists($icfunc))
			{
$all_lines.=$icfunc(1);
			}
		}
	}
	$templates->set_tpl('{$all_lines}',$all_lines);
	return $templates->get_tpl("admin.content_tree");
}


//==================
class watermark3{
    # given two images, return a blended watermarked image
    function create_watermark( $main_img_obj, $watermark_img_obj, $alpha_level = 100 ,$wm=1) {

        $alpha_level    /= 100; # convert 0-100 (%) alpha to decimal
        # calculate our images dimensions
        $main_img_obj_w = imagesx( $main_img_obj );
        $main_img_obj_h = imagesy( $main_img_obj );
        $watermark_img_obj_w    = imagesx( $watermark_img_obj );
        $watermark_img_obj_h    = imagesy( $watermark_img_obj );
        $st=round($main_img_obj_w/13);//echo "$main_img_obj_w $watermark_img_obj_w<br />";
        if($main_img_obj_w<$watermark_img_obj_w)
        {
		$x=floor($main_img_obj_w-$st*2);
		$y=floor($watermark_img_obj_h*($x/$watermark_img_obj_w));
		$watermark_img_obj= newsize($x,$y,$watermark_img_obj_w,$watermark_img_obj_h);
		$watermark_img_obj_w    = imagesx( $watermark_img_obj );
		$watermark_img_obj_h    = imagesy( $watermark_img_obj );
        }
        # determine center position coordinates
         
         
        $main_img_obj_min_x = floor( $st );
        $main_img_obj_min_y = floor( ( $main_img_obj_h  ) - ( $watermark_img_obj_h +$st ) );
        
        # create new image to hold merged changes
        $return_img = imagecreatetruecolor( $main_img_obj_w, $main_img_obj_h );
        # walk through main image
        for( $y = 0; $y < $main_img_obj_h; $y++ ) {

            for( $x = 0; $x < $main_img_obj_w; $x++ ) {

                $return_color   = NULL;
                # determine the correct pixel location within our watermark
                $watermark_x    = $x - $main_img_obj_min_x;
                $watermark_y    = $y - $main_img_obj_min_y;
                # fetch color information for both of our images
                $main_rgb = imagecolorsforindex( $main_img_obj, imagecolorat( $main_img_obj, $x, $y ) );
                # if our watermark has a non-transparent value at this pixel intersection
                # and we're still within the bounds of the watermark image
                if (    $watermark_x >= 0 && $watermark_x < $watermark_img_obj_w &&
                    $watermark_y >= 0 && $watermark_y < $watermark_img_obj_h ) {
                    $watermark_rbg = imagecolorsforindex( $watermark_img_obj, imagecolorat( $watermark_img_obj, $watermark_x, $watermark_y ) );
                    # using image alpha, and user specified alpha, calculate average
                    $watermark_alpha    = round( ( ( 127 - $watermark_rbg['alpha'] ) / 127 ), 2 );
                    $watermark_alpha    = $watermark_alpha * $alpha_level;
                    # calculate the color 'average' between the two - taking into account the specified alpha level
                    $avg_red        = $this->_get_ave_color( $main_rgb['red'],       $watermark_rbg['red'],      $watermark_alpha );
                    $avg_green  = $this->_get_ave_color( $main_rgb['green'], $watermark_rbg['green'],    $watermark_alpha );
                    $avg_blue       = $this->_get_ave_color( $main_rgb['blue'],  $watermark_rbg['blue'],     $watermark_alpha );
                    # calculate a color index value using the average RGB values we've determined
                    $return_color   = $this->_get_image_color( $return_img, $avg_red, $avg_green, $avg_blue );
                # if we're not dealing with an average color here, then let's just copy over the main color
                } else {
                    $return_color   = imagecolorat( $main_img_obj, $x, $y );
                } # END if watermark

                # draw the appropriate color onto the return image

                imagesetpixel( $return_img, $x, $y, $return_color );
            } # END for each X pixel
        } # END for each Y pixel
        # return the resulting, watermarked image for display
        return $return_img;
    } # END create_watermark()
    # average two colors given an alpha

    function _get_ave_color( $color_a, $color_b, $alpha_level ) {

        return round( ( ( $color_a * ( 1 - $alpha_level ) ) + ( $color_b    * $alpha_level ) ) );

    } # END _get_ave_color()

    # return closest pallette-color match for RGB values

    function _get_image_color($im, $r, $g, $b) {

        $c=imagecolorexact($im, $r, $g, $b);

        if ($c!=-1) return $c;

        $c=imagecolorallocate($im, $r, $g, $b);

        if ($c!=-1) return $c;

        return imagecolorclosest($im, $r, $g, $b);

    } # EBD _get_image_color()
} # END watermark API

//==================

      function inet_aton($ip){ 
        $ip = ip2long($ip); 
        ($ip < 0) ? $ip+=4294967296 : true; 
        return $ip; 
      }
?>