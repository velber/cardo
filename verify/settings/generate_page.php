<?

if (function_exists("generate_menu"))generate_menu();
$ending=isset($_GET["print"])?"print_":"";

	$templates->set_tpl('{$center}',$center);
$templates->set_tpl('{$request_url}',$request_url);

$glb["sys_tegs"]=$glb["teg_robots"]?"<meta name='robots' content='noindex,follow' />":"<meta name='robots' content='index,follow' />";
$glb["sys_tegs"].=(isset($glb["canonical"])?"
	<link rel='canonical' href='http://{$ass}{$glb["canonical"]}' >":"");
$glb["sys_tegs"].=(isset($glb["pagination_prev"])?"
	<link rel='prev' href='http://{$ass}{$glb["pagination_prev"]}' >":"");
$glb["sys_tegs"].=(isset($glb["pagination_next"])?"
	<link rel='next' href='http://{$ass}{$glb["pagination_next"]}' >":"");
	
if(!isset($_GET["admin"]))
{
	//die();
	$templates->set_tpl('{$main_title}',$main_title);
	$templates->set_tpl('{$main_keywords}',$main_keywords);
	$templates->set_tpl('{$main_description}',$main_description);

	$templates->set_tpl('{$main_content}',$main_content);
	$templates->set_tpl('{$counters}',$counters);
	$templates->set_tpl('{$sitename}',$ass);
	$templates->set_tpl('{$word}',$_SESSION["word"]);
	$templates->set_tpl('{$price1}',$_SESSION["price1"]);
	$templates->set_tpl('{$price2}',$_SESSION["price2"]);
	$templates->set_tpl('{$last_views}',$last_views);
	$templates->set_tpl('{$selected}',$selected);
	$templates->set_tpl('{$types_list}',$types_list);
	$templates->set_tpl('{$filtres_names}',$filtres_names);
	$templates->set_tpl('{$filters_panel}',$glb["filters_panel"]);
	$templates->set_tpl('{$ass}',$ass);
	$templates->set_tpl('{$cur_show}',$cur_show);
	$templates->set_tpl('{$languages_form}',get_languages_form());
	$templates->set_tpl('{$home_class2}',$home_class2);
	$templates->set_tpl('{$linkmarket_text}',$linkmarket_text);
	$templates->set_tpl('{$carusel}',$carusel);
	$templates->set_tpl('{$right_block}',$glb["right_block"]);
	$templates->set_tpl('{$sys_tegs}',$glb["sys_tegs"]);
	get_translate();
	$time_generate=microtime(1)-$time1;
	$templates->set_tpl('{$time_generate}',$time_generate);
	if(substr_count($request_url2,"?print")>0)
	{
		$center=cleanstring($center);
		$filters_panel=cleanstring($glb["filters_panel"]);
		$filters_panel=$filters_panel!=""?$filters_panel:"test";
		$ret=end(explode('callback=', $request_url2));
		$ret=current(explode("&_=", $ret));
		echo $ret."({'text':'{$center}','filters':'{$filters_panel}'})";
		exit();
	}
	
	echo function_exists("get_new_links")?get_new_links($templates->get_tpl("{$ending}main")):$templates->get_tpl("{$ending}main");
	
}else
{
	$templates->set_tpl('{$admin_top_menu}',$admin_top_menu);
	$templates->set_tpl('{$admin_left_menu}',$admin_left_menu.$admin_left_menu_last);
	$templates->set_tpl('{$admin_menus}',$admin_menus);
	$templates->set_tpl('{$its_name}',$its_name);
	$templates->set_tpl('{$it_item}',$it_item);
	$templates->set_tpl('{$additions_buttons}',$additions_buttons);
	$templates->set_tpl('{$main_tab}',$main_tab);
	$templates->set_tpl('{$langs_select}',$langs_select);
	$templates->set_tpl('{$time_generate}',microtime(1)-$time1);
	//$templates->set_tpl('{$admin_content_tree}',get_admin_content_tree());
	$templates->set_tpl('{$admin_content_tree}',"");
 
	if($_SESSION['status']=="admin")
	{
		echo $templates->get_tpl("{$ending}main");
	}else
	{
		echo $templates->get_tpl("enter");
	}
}
iflongload($time_generate);
?>