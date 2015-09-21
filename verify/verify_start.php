<?php
/**
 * Created by PhpStorm.
 * User: volodini
 * Date: 7/2/15
 * Time: 12:56 PM
 */
ini_set('display_errors', 'on');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
mysql_connect('217.12.201.205', 'zoond_make_r', 'makewear12') or die ('no connect');
mysql_select_db('zoond_make');
$query = "
	SELECT * FROM `parser`
	WHERE `id`='13'
	";
$result = mysql_query($query);
if (mysql_num_rows($result) > 0)
{
    $categories="";

for($i=1;$i<=mysql_num_rows($result);$i++)
    {
        $row = mysql_fetch_object($result);
        $id=$row->id;
        $cat_id=$row->cat_id;
        $h1=$row->h1;
        $img=$row->img;
        $price=$row->price;
        $price2=$row->price2;
        $nonal=$row->no_nal;
        $sizeCol=$row->sizeColor;
        $desc=$row->desc;
        $cod=$row->cod;
        $dopimg=$row->dopimg;
        $per=$row->per;
        $date=$row->date;
        $url=$row->url;
        $from=$row->from;
        $to=$row->to;
//        require("admin.parser.line.php");
  //      $all_lines.=$all_line;
    }
} else {
echo "SQL Error!";
}
$date=date("Y");
$query = "
	UPDATE `parser`
	SET `date`='{$date}'
	WHERE `id`='{$id}'
	";
mysql_query($query);
$query = "SELECT `commodity_ID`, `from_url`, `commodity_price`, `com_sizes`, `select_color`
FROM  `shop_commodity` AS c
INNER JOIN  `shop_commodities-categories` AS cc ON c.`commodity_ID` = cc.commodityID
WHERE cc.categoryID =$cat_id
AND c.commodity_visible=1
";
$data = mysql_query($query);
$links11 = array();
while($row = mysql_fetch_assoc($data)) {
    $links11[] = $row['from_url'];
    $updateData[] = $row;
}
include 'verify.php';
