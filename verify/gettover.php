<?php
/**
 * Created by PhpStorm.
 * User: volodini
 * Date: 7/2/15
 * Time: 6:14 PM
 */

function gettover($new_url, $count2)
{
    mysql_connect('217.12.201.205', 'zoond_make_r', 'makewear12') or die ('no connect');
    mysql_select_db('zoond_make');
    ob_start();
    $updated = false;
    echo "Осталось: {$count2}<br>";
    if ($new_url == "")
        return "";
    $sql = "describe parser";
    $res = mysql_query($sql);
    $row = mysql_fetch_assoc($res);
//----Error HTTP-------------------------------------
    //   if($_SESSION["cat_id"]==46){
    //     $new_url=flfashion($new_url);
    // }
    $updated = false;
    $err = get_http_error($new_url);
    if (!$err) {
        return;
    }
    if ($err == "503" || $err == '303') {
        if ($_SESSION["cat_id"] == 46) {
            echo "FLFashion - changed URL!";
            return;
        }
        $e = get_headers($new_url);
        var_dump($e);
        if ($_SESSION["cat_id"] == 85) {
            echo $new_url;
            $queryNo2 = "UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
            mysql_query($queryNo2) or die("error no_nal");
            echo "<p style='color:red'>Товар не найдено!</p>";
        }
        return;
    }
    if ($err == "404") {
        if ($_SESSION["cat_id"] == 46) {
            echo $new_url . '<br>';
            echo "FLFashion - changed URL!";
            echo "<br>" . $new_url;
            echo "<hr>";
            $content = ob_get_contents();
            file_put_contents($_SESSION['filename'], $content, FILE_APPEND);
            ob_flush();
            ob_end_clean();
            $return = [$content, $updated];
            return $return;
        }
        $e = get_headers($new_url);
        //var_dump($e);
        echo "<br/><h2>Error 404</h2><br/><a href={$new_url} target='_blank' >{$new_url}</a>";
        $updateNonal = "UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
        mysql_query($updateNonal);
        return;
    } else {
        /*   if($_SESSION["cat_id"]==46)            {

            $url = 'http://flfashion.com.ua/novelty.html';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $new_url); // отправляем на
            curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.132 Safari/537.36");
            curl_setopt($ch, CURLOPT_HEADER, 1); // пустые заголовки
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
                curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
            curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/my_cookies.txt'); // сохранять куки в файл
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/my_cookies.txt');
            $total = curl_exec($ch);
            echo $total;
            exit;	
            if (curl_errno($ch))
            {
                print curl_error($ch);
                return;
            }
            curl_close($ch);
        } */
        if ($_SESSION["cat_id"] != 49 && $_SESSION["cat_id"] != 1 && $_SESSION["cat_id"] != 47) {

            $total = file_get_contents($new_url);
        }
    }
//-----------------------------------------
    $total = str_replace("	", "", $total);
    $total = str_replace("  ", "", $total);
    $total = str_replace("&nbsp;", " ", $total);
    $total = str_replace("itemprop", "class", $total);
    $total = str_replace("name='option_3'", 'name="option_3"', $total);
    $total = str_replace("<arel", ' <a rel', $total);
    $total = str_replace('class=""', 'class="imageSellin"', $total);
    if ($total == "")
        return "";
//    echo "<br />Импортирован:{$new_url}<br />";
    $total = str_replace('"></li><li class="size-enable"', '" class="cl_001"></li><li class="size-enable"', $total);
    $total = str_replace("'src", "' src", $total);
    $txt = '<a class="upspa" id="inline" href="#" onclick="document.getElementById(\'windrazmer\').style.display=\'block\'; return false;" title="Таблица размером">Таблица размеров</a>      <div id="windrazmer">  <div class="loginf">   <table id="sizesr-t">  <tbody>  <tr>   <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Международный</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Российский</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем груди</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем талии</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем бедер</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">S</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">42</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">84</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">68</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">92</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">M</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">44</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">88</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">72</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">96</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">L</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">46</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">92</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">76</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">100</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">48</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">96</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">80</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">104</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XXL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">50</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">100</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">84</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">108</td></tr> <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">3XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">52</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">104</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">88</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">112</td></tr>   </tbody>  </table>  *В зависимости от ткани, параметры могут расходиться на +\- 2см!<br> *Все вещи стандартные и соответствуют этой таблице.<br> *S-L - универсальный размер, с тянущейся тканью, подходит на размеры S, M, L.    <div title="Закрыть" class="fancybox-klose" onclick="document.getElementById(\'windrazmer\').style.display=\'none\'; return false;"></div>  </div></div> ';
    $txt2 = ' <a class="upspa" id="inline" href="#" onclick="document.getElementById("windrazmer").style.display="block"; return false;" title="Таблица размером">Таблица размеров</a>        <div id="windrazmer">  <div class="loginf">  <b>Таблица больших размеров:</b><br><br> <table id="sizesr-t">  <tbody>  <tr>     <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Международный</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Российский</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем груди</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем талии</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем бедер</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">48</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">96</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">82</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">106</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XXL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">50</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">100</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">86</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">110</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">3XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">52</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">106</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">92</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">116</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">4XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">54</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">112</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">98</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">122</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">5XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">56</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">118</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">104</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">128</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">6XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">58</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">124</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">110</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">134</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">7XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">60</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">130</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">116</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">140</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">8XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">62</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">136</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">122</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">146</td></tr>   </tbody>  </table>  *В зависимости от ткани, параметры могут расходиться на +- 2см!<br> *Все вещи стандартные и соответствуют этой таблице.<br>  *L-XXL (и другие подобные) - универсальный размер, с
тянущейся тканью,<br>подходит на размеры от L до XXL, т.е.: L, XL, XXL.    <div title="Закрыть" class="fancybox-klose" onclick="document.getElementById("windrazmer").style.display="none"; return false;"></div>  </div></div> ';
    $total = str_replace($txt, "", $total);
    $html = str_get_html($total);
//==========Order===================================
    $order = $_GET["step"] ? $_GET["step"] - 1 : 0;
    echo "Order: " . $order . "<br/>";
//==========Cod=====================================
    $cod = $html->find($_SESSION["cod"], 0)->plaintext;

    //------FlFalshion--------------------
    if ($_SESSION["cat_id"] == 46) {
        $pos = strpos($cod, "-");
        $cod = substr($cod, $pos, strlen($cod));
        $cod = str_replace("- ", "", $cod);
        $cod = str_replace("-", "", $cod);

        $pos2 = strpos($cod, ":");
        if ($pos2 !== false) {
            $cod = strstr($cod, ":");
            $cod = str_replace(":", "", $cod);
            $cod = str_replace(": ", "", $cod);
        }
        $pos22 = strrpos($cod, '"');
        if ($pos22 !== false) {
            $cod = substr($cod, $pos22 + 1, strlen($cod));
            $cod = str_replace(" ", "", $cod);
        }
    }


//    $query="UPDATE `shop_commodity` SET `cod`='{$cod}' WHERE `from_url`='{$new_url}';";
//    mysql_query($query);
    echo "Category: " . $_SESSION["cat_id"] . "<br/>";
    echo "Cod: " . $cod . "<br/>";
//=Name========================================
    $name = trim(str_replace("новый товар", "", $html->find($_SESSION["h1"], 0)->plaintext));
    $name = strip_tags($name);
    $name = trim($name);
    $name = str_replace("&quot;", '"', $name);


    //------FlFashion--------------------
    if ($_SESSION["cat_id"] == 46) {

        $name = str_ireplace("Модель:", "", $name);
        $name = str_replace("МОДЕЛЬ:", "", $name);
        $name = str_replace("-", " ", $name);
        $pos1 = array_values(array_filter(array_unique(explode(" ", $name))));
        $quantity = count($pos1);
        $cod = $pos1[$quantity - 2] . " " . $pos1[$quantity - 1];
        $name = implode(" ", $pos1);
    }

//    $queryn="UPDATE `shop_commodity` SET `com_name`='{$name}' WHERE `from_url`='{$new_url}';";
//    mysql_query($queryn);

//------FlFashion--------------------
    /*if($_SESSION["cat_id"]==46) {
        $queryn = "UPDATE `shop_commodity` SET `com_name`='{$name}', `cod`='{$cod}' WHERE `from_url`='{$new_url}';";
        mysql_query($queryn);
    }*/
    echo "Title: " . $name . "<br/>";


    echo "Url: <a href={$new_url} target='_blank'>{$new_url}</a><br/>";
//    echo "img: <a href={$src} target='_blank'>{$src}</a><br/>";
//    echo "Type Image: ".$typeName2.": ".$srcc."<br/>";

//----------Price-----------------------------------------------
    $price = $html->find($_SESSION["price"], 0)->plaintext;//
    if ($price == "") $price = $html->find($_SESSION["price"], 0)->value;//
    $price = strip_tags($price);
    //------S&L--------------------
    if ($_SESSION["cat_id"] == 48) {
        $price = strstr($price, "Розница");
    }

    $price = htmlentities($price, null, 'utf-8');
    $price = str_replace("&nbsp;", "", $price);
    $price = str_replace(" ", "", $price);
    $price = str_replace(",00", "", $price);
    $price = str_replace(".00", "", $price);
    $price = str_replace("грн.", "", $price);
    $price = str_replace("Цена", "", $price);
    $price = str_replace("цена", "", $price);
    $price = str_replace(":", "", $price);
    $price = str_replace("грн", "", $price);
    $price = str_replace(",", "", $price);
    $price = str_replace("Розница", "", $price);
    $price = str_replace("Стоимость:", "", $price);
    $price = (int)$price;
//=Price_Opt========================================
    $price2 = $html->find($_SESSION["price2"], 0)->plaintext;//
    if ($price2 == "") $price2 = $html->find($_SESSION["price2"], 0)->value;//
    $price2 = strip_tags($price2);
    $price2 = htmlentities($price2, null, 'utf-8');
    $price2 = str_replace("грн", "", $price2);
    $price2 = str_replace("&nbsp;", "", $price2);
    $price2 = str_replace("грн.", "", $price2);
    $price2 = str_replace(",", "", $price2);
    $price2 = str_replace("Розница", "", $price2);
    $price2 = str_replace("Опт ", "", $price2);
    $price2 = (int)$price2;

        //-------Color and Size, Наличии-----------------------
        $nonal2 = $html->find(".color-selector", 0)->plaintext;

        //	echo $nonal."<br/>";
        $nonal2 = str_replace("наличии", "наличии.", $nonal2);
        $pushCol = "<p><span class=sttt2 >Цвет:</span>";
        $col = explode(".", $nonal2);
        for ($i = 0; $i < count($col); $i++) {
            $posCol = strpos($col[$i], "В наличии");
            if ($posCol !== false)
                $pushCol .= ", " . $col[$i];
        }
        $pushCol = str_replace("Цвет:</span>,", "Цвет:</span>", $pushCol);
        $pushCol = str_replace(" В наличии", "", $pushCol);
        if ($pushCol == "") {
            $n = 1;
            echo "<br/>нет наличии";
        } else {
            //	echo $pushCol;
        }
        //------------------------------

        $tbeg = strstr($desc, "<p><span class=sttt2 >Цвет: </span>");
        if ($tbeg) {
            $beg = strpos($tbeg, "<p>");
            $beg1 = strpos($tbeg, "<p>", 1);
            $sub = substr($tbeg, $beg, $beg1);

            $desc = str_replace($sub, $pushCol . "</p>", $desc);
        } else {
            $desc .= $pushCol . "</p>";
        }
        //echo "col: ".$pushCol;
        if ($name == $cod) {
            $pp = strpos($desc, "Стиль");
            if ($pp !== false) {
                $namee = strstr($desc, '<p><span class=sttt2 >Стиль:');
                $namee = strstr($namee, "</p>", true);

                $desc = str_replace($namee . "</p>", "", $desc);
                $namee = str_replace("<p><span class=sttt2 >Стиль: </span> ", "", $namee);
                $namee = str_replace("</p>", "", $namee);
                $namee = ucfirst_mb($namee);
                $name = $namee;
                echo "Name: " . $name;
            }
        } else {
            $namee = strstr($desc, '<p><span class=sttt2 >Стиль:');
            $namee = strstr($namee, "</p>", true);
            $desc = str_replace($namee . "</p>", "", $desc);
        }

//    echo $desc;

    //------Agio-Z--Seventeen--FlFashion----------------
    if (($_SESSION["cat_id"] == 45) || $_SESSION["cat_id"] == 47 || $_SESSION["cat_id"] == 46) {
        $per = $_SESSION["per"];
        $price2 = $price;
        $price += $per;

    }

    $query = "UPDATE `shop_commodity` SET `commodity_price`='{$price}', `commodity_price2`='{$price2}' WHERE  `from_url`='{$new_url}';";
    mysql_query($query) or die("Error price");
    //	die();
    echo "Per: " . $_SESSION["per"] . "<br/>";
    echo "Price: {$price}";
    echo "<br/>Opt: {$price2}<br/>";

//------------------------------------------------------
//------------------------------------------------------
    $q = mysql_query("SELECT * FROM `shop_commodity`") or die("Error select");

    //===========Search========================

    for ($i = 0; $i < mysql_num_rows($q); $i++) {
        $f = mysql_fetch_array($q);
        if ($new_url == $f['from_url']) {
            //	echo "ComId: ".$f['commodity_ID']."<br/>";
            $comid = $f['commodity_ID'];
        }
    }
//------S&L select color and size------------------------
    $selSize2 = array();

    $selSize = $html->find($_SESSION["sizeCol"]);
    //--------Fashioup--Meggi-FStyle-Seventeen-flfashion-----------------
    if ($_SESSION["cat_id"] == 1 || $_SESSION["cat_id"] == 2 || $_SESSION["cat_id"] == 42 || $_SESSION["cat_id"] == 46 || $_SESSION["cat_id"] == 47 || $_SESSION["cat_id"] == 63 || $_SESSION["cat_id"] == 65) {
        foreach ($selSize as $key => $a) {
            $selSize2[$key] .= $a->plaintext;
        }
        $selSize = implode(";", $selSize2);
        $selSize = str_replace("Без пояса", "", $selSize);
        $selSize = str_replace("С поясом (+25грн.)", "", $selSize);
        $selSize = str_replace(";;", "", $selSize);
    }


        //-----Select Size and Color------
     /*   $selSize3 = "";
        $selSizee = $html->find($selectorColor);
        $select = "<select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
        foreach ($selSizee as $a) {
            $selSize3 .= $a->plaintext . ";";
            if ($a->plaintext != "Не определено")
                $select .= '<option value="' . $a->plaintext . '" >' . $a->plaintext . '</option>';
        }
        $select .= "</select>";
        $checksel = strip_tags($select);
        if ($checksel == "") {
            $select = "";
        }

        $selColSize = $select . $txt22;
        $checksell = strip_tags($selColSize);
        if ($checksell == "") {
            $selColSize = "";
        }
        $selSize = $selSize2;
*/

    if ($selSize != "") {
        echo "Size: " . $selSize . "<br>";
        $query = "UPDATE `shop_commodity` SET `com_sizes`='{$selSize}',`select_color`=''  WHERE  `from_url`='{$new_url}';";
        mysql_query($query) or die("Error select2");
    }

    if ($selColSize != "") {
        echo "SizeColor: <br>" . $selColSize;
        $query = "UPDATE `shop_commodity` SET `select_color`='{$selColSize}'  WHERE  `from_url`='{$new_url}';";
        mysql_query($query) or die("Error select2");
    }

    foreach ($_SESSION['updateData'] as $updateData) {
        if ($comid == $updateData['commodity_ID']) {
            if (
                $updateData['commodity_price'] != $price ||
                $updateData['com_sizes'] != $selSize ||
                $updateData['select_color'] != $selColSize
            ) {
                $updated = true;
            }
        }
    }
        sleep(1);
    echo "<hr>";
    $content = ob_get_contents();
    file_put_contents($_SESSION['filename'], $content, FILE_APPEND);
    ob_flush();
    ob_end_clean();
    $return = [$content, $updated];
    return $return;
}
