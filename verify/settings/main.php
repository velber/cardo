<?
if($_GET["admin"]!="")
{
	$_SESSION["upload_dir"]="uploads";
	if(!get_magic_quotes_gpc())
	{
		fix_magic_quotes_gpc();
	}

	$_SESSION["lastpage2"]=$_SESSION["lastpage2"]!=$_SESSION["lastpage"]?$_SESSION["lastpage"]:$_SESSION["lastpage2"];
	$_SESSION["lastpage"]=$_SESSION["currentpage"]!=$_SESSION["lastpage"]?$_SESSION["currentpage"]:$_SESSION["lastpage"];
	$_SESSION["currentpage"]=$glb["request_url"];
}
$query="SELECT * FROM `domens` WHERE `domen`='{$ass}' OR `www_domen`='{$ass}';";
$result = mysql_query($query);
if (mysql_num_rows($result) > 0) 
{
		
}else
{
	$query="SELECT * FROM `domens` WHERE `domenID`=0;";
	$result = mysql_query($query);
}

	$row = mysql_fetch_object($result);
	$domen_ID=$row->domenID;
	$domenID=$domen_ID;
	$doman_name=$row->domen;
	$theme_name=$row->theme_name;
	$watermark=$row->watermark;
	$comitemst=$row->comitemst;
	$comitemsx=$row->comitemsx;
	$comitemsy=$row->comitemsy;
	$catitemscount=$row->catitemscount;
	$catitemsx=$row->catitemsx;
	$catitemsy=$row->catitemsy;
	$catitemst=$row->catitemst;
	$addcomimgx=$row->addcomimgx;
	$addcomimgy=$row->addcomimgy;
	$addcomimgt=$row->addcomimgt;
	$artimgx=$row->artimgx;
	$artimgy=$row->artimgy;
	$artimgt=$row->artimgt;
	$artimgaddx=$row->artimgaddx;
	$artimgaddy=$row->artimgaddy;
	$artimgaddt=$row->artimgaddt;
	$urlend=$row->urlend;
	$sys_lng=$row->lng_id;
	$global_meil=$row->email!=""?$row->email:$global_meil;
	$glb["theme_name"]=$theme_name;	
	$domen_theme=$theme_name;

	$_SESSION['sel_lang']=isset($_POST["sel_lang"])?$_POST["sel_lang"]:$_SESSION['sel_lang'];
	$_SESSION['sel_lang']=isset($_GET["sel_lang"])?$_GET["sel_lang"]:$_SESSION['sel_lang'];
	$sys_lng=is_numeric($_SESSION['sel_lang'])?$_SESSION['sel_lang']:$sys_lng;
	$sel_lang=is_numeric($_SESSION['sel_lang'])?$_SESSION['sel_lang']:1;
	$glb["sys_lng"]=$sys_lng;
	$query2 = "SELECT * FROM `languages` ORDER BY `languages_id`;";
	$result2 = mysql_query($query2);
	if (mysql_num_rows($result2) > 0)
	{
		$langs_options="";
		for($i=1;$i<=mysql_num_rows($result2);$i++)
		{
			$row2 = mysql_fetch_object($result2);
			$languages_id=$row2->languages_id;
			$lng_name=$row2->name;
			$selected=$sel_lang==$languages_id?"selected":"";
			$selected2=$sel_lang==$languages_id?"checked":"";
			$langs_options=$langs_options." <option value='{$languages_id}' {$selected}>{$lng_name}</option>";		
			$langs_options2.="
			<li>
									<input type='radio' id='filter{$languages_id}' {$selected2} onchange='this.form.submit();' name='filter{$languages_id}' value='{$languages_id}'>
									<label for='filter{$languages_id}'><span>{$lng_name}</span></label>
								</li>
			";		
		}			
	}

	$langs_select="
	<form method='POST' action='{$request_url}'>
		<nobr>Выбор языка:</nobr>
		<select name='sel_lang' onchange='javascript:lng_select(\"{$request_url}\");' style='position: relative; width: 80px;' id='lng_selecting'>
			{$langs_options}
		</select>
	</form>
	";
	
		$langs_select2="
	<form method='POST' action='{$request_url}'>
		
		
		{$langs_options2}
		
	</form>
	";

	$query="SELECT * FROM `domens_description` WHERE `dom_id`='{$domen_ID}' AND `lng_id`='{$sys_lng}';";
	$result = mysql_query($query);
	$row = mysql_fetch_object($result);
	if($request_url=="/")
	{
		$h_title=$row->title;
		$h_keywords=$row->keywords;
		$h_description=$row->description;
		$h_content=$row->content;
		$main_page_title=$row->main_page_title;
		$main_page_text=$row->main_page_text;
	}else
	{
		$h_title=$gallery_domen;
		$h_keywords=$gallery_domen;
		$h_description=$gallery_domen;
		$h_content=$gallery_domen;
	}

$theme_name=$_GET["admin"]!=""?"admin":$theme_name;

$dostavka_defcur=1;



$templates=new templ();
$templates->set_tpl('{$urlend}',$urlend);
$templates->set_tpl('{$theme_name}',$theme_name);
$templates->set_tpl('{$request_url}',$request_url);
$templates->set_tpl('{$url_tree}',"");
$templates->set_tpl('{$cms_ver}',$cms_ver);
$templates->set_tpl('{$filters}',$filters);
$templates->set_tpl('{$framework}',"plusstrap");

$glb["domen_id"]=$domen_ID;
$glb["dom_mail"]=str_replace("www.","",$ass);
$glb["sys_mail"]=$global_meil;
$glb["main_page_title"]=$main_page_title;
$glb["sys_lng"]=$sys_lng;
$glb["urlend"]=$urlend;
$glb["title"]=$title;
$glb["description"]=$description;
$glb["keywords"]=$keywords;
$glb["content"]=$content;
$glb["templates"]=$templates;
$glb["gallery_domen"]=$gallery_domen;
$glb["watermark"]=$watermark;
cur_function();
countr();
?>