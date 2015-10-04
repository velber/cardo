<?php

function getContentViaCurl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); // отправляем на
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0");
    curl_setopt($ch, CURLOPT_HEADER, 0); // пустые заголовки
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
    curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__) . '/my_cookies.txt'); // сохранять куки в файл
    curl_setopt($ch, CURLOPT_COOKIEFILE, '/my_cookies.txt');
    $total = curl_exec($ch);
    $error = curl_errno($ch);
//    $httpcdd = curl_getinfo($handler, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($total, $error);
}

function gettover($new_url, $count2)
{
    mysql_connect('217.12.201.205', 'zoond_make_r', 'makewear12') or die ('no connect');
    mysql_select_db('zoond_make');
    ob_start();
    echo "Осталось: {$count2}<br>";
    if ($new_url == "") {
        echo "Нету ссылки!";
        return "";
    }
    $updated = false;
    $content = getContentViaCurl($new_url);
    if ($content[1]) {
        var_dump($content[1]);
        echo "<h2 style='color:red'>CURL_ERROR!</h2>";
    return '';
    /* if ($err == "503" || $err == '303' || $err == '404') {
         if ($_SESSION["cat_id"] == 46) {
             echo "FLFashion - changed URL!" . $new_url;
             return obReturn($updated);
         }
     }
*/
    } else {
        $total = $content[0];
        if (!$total) {
            echo "The page is not availaeble! -- " . $new_url;
            return obReturn($updated);
        }
    }
    $total = str_replace("	", "", $total);
    if ($total == "")
        return "";
    $total = str_replace($txt, "", $total);
    $html = str_get_html($total);
//==========Order===================================
    $order = $_GET["step"] ? $_GET["step"] - 1 : 0;
    echo "Order: " . $order . "<br/>";
//==========Cod=====================================
    $cod = $html->find($_SESSION["cod"], 0)->plaintext;
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
//===========Name========================================
    $name = trim(str_replace("новый товар", "", $html->find($_SESSION["h1"], 0)->plaintext));
    $name = strip_tags($name);
    $name = trim($name);
    $name = str_replace("&quot;", '"', $name);
    if ($_SESSION["cat_id"] == 46) {
        $name = str_ireplace("Модель:", "", $name);
        $name = str_replace("МОДЕЛЬ:", "", $name);
        $name = str_replace("-", " ", $name);
        $pos1 = array_values(array_filter(array_unique(explode(" ", $name))));
        $quantity = count($pos1);
        $cod = $pos1[$quantity - 2] . " " . $pos1[$quantity - 1];
        $name = implode(" ", $pos1);
    }
    /*if($_SESSION["cat_id"]==46) {
        $queryn = "UPDATE `shop_commodity` SET `com_name`='{$name}', `cod`='{$cod}' WHERE `from_url`='{$new_url}';";
        mysql_query($queryn);
    }*/
    echo "Title: " . $name . "<br/>";
    echo "Url: <a href={$new_url} target='_blank'>{$new_url}</a><br/>";

//----------Price-----------------------------------------------
    $price = $html->find($_SESSION["price"], 0)->plaintext;//
    if ($price == "") $price = $html->find($_SESSION["price"], 0)->value;//
    $price = strip_tags($price);
    $price = preg_replace("/\\D/", "", $price);
    $price = (int)$price;
//========Price_Opt========================================
    if ($_SESSION["cat_id"] == 46) {
        $per = $_SESSION["per"];
        $price2 = $price;
        $price += $per;
    }
    $query = "UPDATE `shop_commodity` SET `commodity_price`='{$price}', `commodity_price2`='{$price2}' WHERE  `from_url`='{$new_url}';";
    mysql_query($query) or die("Error price");
    echo "Per: " . $_SESSION["per"] . "<br/>";
    echo "Price: {$price}";
    echo "<br/>Opt: {$price2}<br/>";
//===========Size=========================================
    $selSize2 = array();
    $selSize = $html->find($_SESSION["sizeCol"]);
    if ($_SESSION["cat_id"] == 46) {
        foreach ($selSize as $key => $a) {
            $selSize2[$key] .= $a->plaintext;
        }
        $selSize = implode(";", $selSize2);
        $selSize = str_replace("Без пояса", "", $selSize);
        $selSize = str_replace("С поясом (+25грн.)", "", $selSize);
        $selSize = str_replace(";;", "", $selSize);
    }
    if ($selSize != "") {
        echo "Size: " . $selSize . "<br>";
        $query = "UPDATE `shop_commodity` SET `com_sizes`='{$selSize}',`select_color`=''  WHERE  `from_url`='{$new_url}';";
        mysql_query($query) or die("Error select2");
    }
//=========Parser Interface=============================
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