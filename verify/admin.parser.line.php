<?
$all_line="
<tr id='{$id}' rel='parser'  rel2='id' style='height:73px;'>
	<td>
		#{$id}
	</td>
	<td  id='name' class='cl_edit'>
		{$name}
	</td>
	<td  id='date' class='cl_edit'>
		{$date}
	</td>
	<td id='url' class='cl_edit'>
		{$url}
	</td>
	<td id='from' class='cl_edit'>
		{$from}
	</td>
	<td id='to' class='cl_edit' style='width:180px;'>
		{$to}
	</td>
	<td id='im_url' class='cl_edit'>

		{$im_url}

	</td>
	<td id='a_href' class='cl_edit'>
		{$ahref}
	</td>
	<td id='links11' class='cl_edit54' >
		<div style='max-height:50px;overflow:hidden;'>{$links11}</div>
	</td>
	<td id='cat_id' class='cl_edit'>
		{$cat_id}
	</td>

	<td id='h1' class='cl_edit'>
		{$h1}
	</td>
	<td id='img' class='cl_edit'>
		{$img}
	</td>
	<td id='price' class='cl_edit'>
		{$price}
	</td>
	<td id='price2' class='cl_edit'>
		{$price2}
	</td>
	<td id='no_nal' class='cl_edit'>
		{$nonal}
	</td>
	<td id='sizeColor' class='cl_edit'>
		{$sizeCol}
	</td>
	<td id='desc' class='cl_edit'>
		{$desc}
	</td>
	<td id='cod' class='cl_edit'>
		{$cod}
	</td>
	<td id='dopimg' class='cl_edit'>
		{$dopimg}
	</td>
	<td id='per' class='cl_edit'>
		{$per}
	</td>
	<td class='acty'>
		<a href='/parser2.php?id={$id}' method='POST' target='_blank' title='$name'><img src='/templates/{$theme_name}/images/play.png' style='height:18px;'></a>
	</td>
	<td class='acty'>
		<a href='upprice.php?id={$id}' target='_blank' title='$name'><img src='/templates/{$theme_name}/images/update.png' style='height:18px;'></a>
	</td>
	<td class='acty'>
		<a href='page.php?id={$id}' target='_blank' title='$name'><img src='/templates/{$theme_name}/img/import.png' style='height:18px;'></a>
	</td>
		<td class='acty'>
		<a href='/?admin=edit_parser&id={$id}'><img src='/templates/{$theme_name}/img/btnbar_edit.png'></a>
	</td>
	<td class='acty'>
		<a href='/?admin=delete_parser&id={$id}'><img src='/templates/{$theme_name}/img/btnbar_del.png'></a>
	</td>
</tr>

";

?>