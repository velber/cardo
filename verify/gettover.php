<?php
/**
 * Created by PhpStorm.
 * User: volodini
 * Date: 7/2/15
 * Time: 6:14 PM
 */

function gettover($new_url, $count2)
{
	ob_start();
	    echo "Осталось: {$count2}<br>";
    if($new_url=="")
        return "";
    $sql="SELECT * FROM `shop_commodity`
		WHERE `from_url`='{$new_url}';";
    $row=mysql_fetch_assoc(mysql_query($sql));
//----Error HTTP-------------------------------------
 //   if($_SESSION["cat_id"]==46){
   //     $new_url=flfashion($new_url);
   // }
$updated = false;
    $err=get_http_error($new_url);
    if (!$err) {
        return;
    }
    if($err=="503"||$err == '303'){
    	 if ($_SESSION["cat_id"]==46) {
    	  	   echo "FLFashion - changed URL!";
    	  	   return;
    	  }
        $e=get_headers($new_url);
        var_dump($e);
        if ($_SESSION["cat_id"] == 85){
            echo $new_url;
            $queryNo2="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
            mysql_query($queryNo2) or die("error no_nal");
            echo "<p style='color:red'>Товар не найдено!</p>";
        }
        return;
    }
    if($err=="404"){
    	  if ($_SESSION["cat_id"]==46) {
    	  	echo $new_url.'<br>';
    	  	   echo "FLFashion - changed URL!";
    	           echo "<br>".$new_url;
                   echo "<hr>";   
    $content = ob_get_contents();
    file_put_contents($_SESSION['filename'], $content, FILE_APPEND);
    ob_flush();
    ob_end_clean();
    $return = [$content, $updated];
    return $return;
    	  }
        $e=get_headers($new_url);
        //var_dump($e);
        echo "<br/><h2>Error 404</h2><br/><a href={$new_url} target='_blank' >{$new_url}</a>";
        $updateNonal="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
        mysql_query($updateNonal);
        return;
    }else {
        if($_SESSION["cat_id"]==49)            {
            $url = 'http://sk-house.ua/Products/SetCurrency?cur=%D0%93%D0%A0%D0%9D';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url); // отправляем на
            curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0");
            curl_setopt($ch, CURLOPT_HEADER, 0); // пустые заголовки
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
            curl_setopt($ch, CURLOPT_REFERER, $new_url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
            curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/my_cookies.txt'); // сохранять куки в файл
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/my_cookies.txt');
            $total = curl_exec($ch);
            if (curl_errno($ch))
            {
                print curl_error($ch);
                return;
            }
            curl_close($ch);
        }
        if($_SESSION["cat_id"]==47)            {
            sleep(3);
            $url = 'http://www.google.com.ua';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $new_url); // отправляем на
            curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0");
            curl_setopt($ch, CURLOPT_HEADER, 0); // пустые заголовки
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
//                curl_setopt($ch, CURLOPT_REFERER, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// просто отключаем проверку сертификата
            curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__).'/my_cookies.txt'); // сохранять куки в файл
            curl_setopt($ch, CURLOPT_COOKIEFILE, '/my_cookies.txt');
            $total = curl_exec($ch);
            if (curl_errno($ch))
            {
                print curl_error($ch);
                return;
            }
            curl_close($ch);
        }
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
        if($_SESSION["cat_id"]!=49 && $_SESSION["cat_id"]!=1 && $_SESSION["cat_id"]!=47){

            $total=file_get_contents($new_url);
        }
    }
//-----------------------------------------
    $total=str_replace("	","",$total);
    $total=str_replace("  ","",$total);
    $total=str_replace("&nbsp;"," ",$total);
    $total=str_replace("itemprop","class",$total);
    $total=str_replace("name='option_3'",'name="option_3"',$total);
    $total=str_replace("<arel",' <a rel',$total);
    $total=str_replace('class=""','class="imageSellin"',$total);
    if($total=="")
        return "";
//    echo "<br />Импортирован:{$new_url}<br />";
    $total=str_replace('"></li><li class="size-enable"','" class="cl_001"></li><li class="size-enable"',$total);
    $total=str_replace("'src","' src",$total);
    $txt='<a class="upspa" id="inline" href="#" onclick="document.getElementById(\'windrazmer\').style.display=\'block\'; return false;" title="Таблица размером">Таблица размеров</a>      <div id="windrazmer">  <div class="loginf">   <table id="sizesr-t">  <tbody>  <tr>   <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Международный</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Российский</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем груди</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем талии</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем бедер</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">S</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">42</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">84</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">68</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">92</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">M</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">44</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">88</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">72</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">96</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">L</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">46</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">92</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">76</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">100</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">48</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">96</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">80</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">104</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XXL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">50</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">100</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">84</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">108</td></tr> <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">3XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">52</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">104</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">88</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">112</td></tr>   </tbody>  </table>  *В зависимости от ткани, параметры могут расходиться на +\- 2см!<br> *Все вещи стандартные и соответствуют этой таблице.<br> *S-L - универсальный размер, с тянущейся тканью, подходит на размеры S, M, L.    <div title="Закрыть" class="fancybox-klose" onclick="document.getElementById(\'windrazmer\').style.display=\'none\'; return false;"></div>  </div></div> ';
    $txt2=' <a class="upspa" id="inline" href="#" onclick="document.getElementById("windrazmer").style.display="block"; return false;" title="Таблица размером">Таблица размеров</a>        <div id="windrazmer">  <div class="loginf">  <b>Таблица больших размеров:</b><br><br> <table id="sizesr-t">  <tbody>  <tr>     <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Международный</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Российский</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем груди</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем талии</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">Объем бедер</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">48</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">96</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">82</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">106</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">XXL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">50</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">100</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">86</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">110</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">3XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">52</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">106</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">92</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">116</td></tr>   <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">4XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">54</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">112</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">98</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">122</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">5XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">56</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">118</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">104</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">128</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">6XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">58</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">124</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">110</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">134</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">7XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">60</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">130</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">116</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">140</td></tr>  <tr> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">8XL</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">62</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">136</td> <td style="text-align: center;border: 1px solid;padding: 5px 10px;">122</td><td style="text-align: center;border: 1px solid;padding: 5px 10px;">146</td></tr>   </tbody>  </table>  *В зависимости от ткани, параметры могут расходиться на +- 2см!<br> *Все вещи стандартные и соответствуют этой таблице.<br>  *L-XXL (и другие подобные) - универсальный размер, с
тянущейся тканью,<br>подходит на размеры от L до XXL, т.е.: L, XL, XXL.    <div title="Закрыть" class="fancybox-klose" onclick="document.getElementById("windrazmer").style.display="none"; return false;"></div>  </div></div> ';
    $total=str_replace($txt,"",$total);
    $html=str_get_html($total);
//==========Order===================================
    $order = $_GET["step"] ? $_GET["step"] - 1 : 0 ;
    echo "Order: ".$order."<br/>";
//==========Cod=====================================
    $cod=$html->find($_SESSION["cod"],0)->plaintext;
    if($_SESSION["cat_id"]==58){
        $title = $html->find('.col-lg-6 h1');
        foreach ($title as $h1) {
            if (trim($h1->plaintext) == 'ПРОДАНО')  {
                $queryNo2="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
                mysql_query($queryNo2) or die("error no_nal");
                return;
            }
        }
    }
    if ($_SESSION["cat_id"]==43) {
        $title = $html->find('.description');
        $escape = true;
        foreach ($title as $span) {
            $absent = trim($span->plaintext);
            $aa = preg_match('/В наличии/',$absent);
            if (preg_match('/В наличии/',$absent))  {
                $escape = false;
            }
        }
        if ($escape) {
            $queryNo2="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
            mysql_query($queryNo2) or die("error no_nal");
            echo "<p style='color:red'>В наличии  - не найдено!</p>";
            return;
        }
    }

    //------FashionUp--------------------
    if($_SESSION["cat_id"]==2){
        $cod=str_replace("артикул: ","",$cod);
//        $cod=str_replace($ag,"",$cod);

    }
    //------Swirl by Swirl-MamaMia-Tutsi--------------------
    if(($_SESSION["cat_id"]==3) || ($_SESSION["cat_id"]==5) || ($_SESSION["cat_id"]==14)){
        $cod=substr($cod,0,5);
        $cod=str_replace(" ","",$cod);
    }
    //------Sellin--------------------
    if($_SESSION["cat_id"]==23){
        $pos=strpos($cod," ");
        $cod=substr($cod,$pos,strlen($cod));
        $cod=str_replace(" ","",$cod);
    }
    //------Seventeen--------------------
    if($_SESSION["cat_id"]==47){
        //$pos=strpos($cod," ");
        //$cod=substr($cod,$pos,strlen($cod));
        //$cod=str_replace(" ","",$cod);
        $cod=preg_replace("/\D/","",$cod);
        if($cod == "") {
        	echo "BLOCK!!!!!";
        	return;
        }
    }
    //------FlFalshion--------------------
    if($_SESSION["cat_id"]==46){
        $pos=strpos($cod,"-");
        $cod=substr($cod,$pos,strlen($cod));
        $cod=str_replace("- ","",$cod);
        $cod=str_replace("-","",$cod);

        $pos2=strpos($cod,":");
        if($pos2!==false){
            $cod=strstr($cod,":");
            $cod=str_replace(":","",$cod);
            $cod=str_replace(": ","",$cod);
        }
        $pos22=strrpos($cod,'"');
        if($pos22!==false){
            $cod=substr($cod,$pos22+1,strlen($cod));
            $cod=str_replace(" ","",$cod);
        }
    }
    //------Meggi--------------------
    if($_SESSION["cat_id"]==42){
        //$cod = preg_replace('/\D/', '', $cod);
        $cod=strstr($cod,"- ");
        $cod=str_replace("Новинка","",$cod);
        $cod=str_replace("- ","",$cod);
    }
    if($_SESSION["cat_id"]==43){
        $search=array("Модель: ","Наличие: ","В наличии","артикул: ");
        $cod=str_replace($search, "", $cod);
    }
    //------Agio-Z-------------------
    if($_SESSION["cat_id"]==45){
        $ag=array("Платье ",'"',"Комбидресс ","летнее","нарядное","Платье-костюм ","Футболка ","-блузка","-рубашка","Кофта ","Блуза ","Брюки ","Болеро ","Свитшот ","Свитер ","Кофточка ","Туника","летний","Сарафан","Лосины ","Костюм ","Блуза-двойка ","Комбинезон ","Жилет ","Нарядное платье ","Юбка ","Юбка-шорты","Платье-туника");
        $cod=str_replace($ag,"",$cod);
        $cod=str_replace('"','',$cod);
        $cod=str_replace(" ","",$cod);
    }
    //------S&L-------------------
    if($_SESSION["cat_id"]==48){
        $as=strpos($cod,"№ ");
        $cod=substr($cod,$as,strlen($cod));
        $cod=str_replace("№ ","",$cod);
    }
    //------Lenida--------------------
    if($_SESSION["cat_id"]==16){
        $posl=strpos($cod,'"');
        $sub=substr($cod,0,$posl+1);
        $cod=str_replace($sub,"",$cod);

        $posl2=strpos($cod,'"');
        $sub2=substr($cod,$posl2,strlen($cod));
        $cod=str_replace($sub2,"",$cod);

    }
    //------Majaly--------------------
    if($_SESSION["cat_id"]==65){
        $title = $html->find('.b-product__data');
        $escape = true;
        foreach ($title as $span) {
            $absent = trim($span->plaintext);
            $aa = preg_match('/В наличии/',$absent);
            if (preg_match('/В наличии/',$absent))  {
                $escape = false;
            }
        }
        if ($escape) {
            $queryNo2="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
            mysql_query($queryNo2) or die("error no_nal");
            echo "<p style='color:red'>В наличии  - не найдено!</p>";
            return;
        }
        //	$cod = htmlentities($cod, null, 'utf-8');
        $maj=strpos($cod,'&#34;');
        if($maj!==false){
            $cod=str_replace('&#34;',"|",$cod);
            $cod=strstr($cod,"|");
            $mpos=strpos($cod,"|");
            $mpos2=strrpos($cod,"|");

            if($mpos!=$mpos2){
                $cod=substr($cod,0,$mpos2);
            }

            $m=strpos($cod,'-');
            if($m!==false){
                $cod=strstr($cod,'-',true);
                $cod=str_replace(" ","",$cod);
            }
            $m2=strpos($cod,',');
            if($m2!==false){
                $cod=strstr($cod,',',true);
            }
            $cod=str_replace("|","",$cod);
            $cod=str_replace(" ","",$cod);
            $cod=str_replace("тыде","ты де",$cod);
        }else{
            $maa=strstr($cod,'(');
            $ma22=strpos($maa,')');
            $codd=substr($maa,0,$ma22+2);
            $cod=str_replace($codd,"",$cod);

            $cod=str_replace(",","",$cod);
            $cod=str_replace(", ","",$cod);
            if((strpos($cod,"атье"))!=false){
                $cod=strstr($cod,"атье");
                $cod=str_replace("атье ","",$cod);
            }
            if((strpos($cod,"остюм"))!=false){
                $cod=strstr($cod,"остюм");
                $cod=str_replace("остюм ","",$cod);
            }
            $mp=strpos($cod," ");
            $cod=substr($cod,0,$mp);
            $cod=str_replace(" ","",$cod);
        }
        $cod=mb_strtolower($cod,'utf-8');
        $cod=mb_substr(mb_strtoupper($cod,'utf-8'),0,1,'utf-8').mb_substr($cod,1,strlen($cod),'utf-8');
    }

    //----SKHouse-------------------
    if($_SESSION["cat_id"]==49){
        $strsk = strstr($cod,"Артикул:");
        $cod=str_replace("Артикул:","",$strsk);

        $strsk2 = strstr($cod,"Описание");
        $cod=str_replace($strsk2,"",$cod);
    }

    //----OlisStyle-----------------
    if($_SESSION["cat_id"]==58){
        $cod=preg_replace("/\D/","",$cod);
    }
    //----Nelli_co-----------------
    if($_SESSION["cat_id"]==62){
        $cod=str_replace("Код: ","",$cod);
    }
    //----FStyle-----------------
    if($_SESSION["cat_id"]==63){
        $cod=str_replace("арт","",$cod);
        $cod=str_replace(" ","",$cod);
    }
    //----Sergio Torri-----------------
    if($_SESSION["cat_id"]==85){
        $cod=preg_replace("/\D/","",$cod);
    }
    //----B1-----------------
    if($_SESSION["cat_id"]==64){
        $b=strpos($cod,"B1");
        if($b!==false){
            $cod=strstr($cod,"B1");
            $cod=str_replace("B1","",$cod);
            $cod=str_replace("B1 ","",$cod);
        }
    }
//    $query="UPDATE `shop_commodity` SET `cod`='{$cod}' WHERE `from_url`='{$new_url}';";
//    mysql_query($query);
    echo "Category: ".$_SESSION["cat_id"]."<br/>";
    echo "Cod: ".$cod."<br/>";
//=Name========================================
    $name=trim(str_replace("новый товар","",$html->find($_SESSION["h1"],0)->plaintext));
    $name=strip_tags($name);
    $name=trim($name);
    $name=str_replace("&quot;",'"',$name);

    //------FashionUp--------------------
    if($_SESSION["cat_id"]==2){
        //$name=str_replace('"',"",$name);
    }
    //------Alva--------------------
    if($_SESSION["cat_id"]==43){
        $str=substr($name,0,strlen($name)-4);
        $name=$str;
    }
    //------Lenida--------------------
    if($_SESSION["cat_id"]==16){
        //	$name=$name;
        /*	$query="
                    UPDATE `shop_commodity`
                    SET
                    `com_name`='{$name}'
                    WHERE `from_url`='{$new_url}'
                    ;";

                    mysql_query($query);*/
    }
    //------Meggi--------------------
    if($_SESSION["cat_id"]==42){
        $name=str_replace("Новинка","",$name);
        $name=strstr($name,"-",true);
        $name=str_replace(" ","",$name);
    }
    //------Swirl by Swirl-MamaMia-Tutsi--------------------
    if(($_SESSION["cat_id"]==3) || ($_SESSION["cat_id"]==5) || ($_SESSION["cat_id"]==14)){
        $name2=substr($name,6,strlen($name));
        $nsea=array("пл","ко","бл","юб");
        $nsea2=array("Пл","Ко","Бл","Юб");
        $name=str_replace($nsea,$nsea2,$name2);
    }
    //------Seventeen--------------------
    if($_SESSION["cat_id"]==47){
        $pos=strpos($name," ");
        $name=substr($name,0,$pos);
        $name=str_replace(" ","",$name);
    }
    //------FlFashion--------------------
    if($_SESSION["cat_id"]==46){

        $name = str_ireplace("Модель:", "", $name);
        $name = str_replace("МОДЕЛЬ:", "", $name);
        $name=str_replace("-"," ",$name);
        $pos1 = array_values(array_filter(array_unique(explode(" ", $name))));
        $quantity = count($pos1);
        $cod = $pos1[$quantity-2]." ".$pos1[$quantity-1];
        echo "<br>Kod - ".$cod."<br>";
        $name = implode(" ", $pos1);
}
/*
   $pos1=strpos($name,":");
        $pos2=strrpos($name,'-');
        if($pos2===false){
            $name=strstr($name,":",true);
        }else {
            $name=substr($name,$pos1,$pos2);
        }
        //	$name=str_replace(" ","",$name);
        $name=str_replace(" Модель","",$name);
        $name=str_replace(": ","",$name);
        $name=str_replace(":","",$name);
        $name=str_replace(" -","",$name);
        $name=str_replace("-","",$name);
        $name=str_replace($cod,"",$name);
    }*/
    //----OlisStyle-----------------
    if($_SESSION["cat_id"]==58){
        $name=str_replace("оптовая цена","",$name);
    }

    //----FStyle-----------------
    if($_SESSION["cat_id"]==63){
        $name=str_replace("арт ","",$name);
        $name=str_replace("арт","",$name);
        $name=str_replace($cod,"",$name);
    }
    //----B1-----------------
    if($_SESSION["cat_id"]==64){
        $b=strpos($name,"B1");
        if($b!==false){
            $name=strstr($name,"B1",true);
            $name=str_replace("B1","",$name);
            $name=str_replace("B1 ","",$name);
            $name=str_replace(" B1","",$name);
        }
    }
    //----Sergio Torri-----------------
    if($_SESSION["cat_id"]==85){
        $name=preg_replace("/\d/","",$name);
    }
//    $queryn="UPDATE `shop_commodity` SET `com_name`='{$name}' WHERE `from_url`='{$new_url}';";
//    mysql_query($queryn);

//------FlFashion--------------------
    /*if($_SESSION["cat_id"]==46) {
        $queryn = "UPDATE `shop_commodity` SET `com_name`='{$name}', `cod`='{$cod}' WHERE `from_url`='{$new_url}';";
        mysql_query($queryn);
    }*/
    echo "Title: ".$name."<br/>";
//=Image========================================
//    $lowsrc_dix=$html->find($_SESSION["img"],0)->alt;
//    //	$lowsrc_dix2=str_replace("/77x117/","/",$lowsrc_dix);
//    if(substr_count($lowsrc_dix,'.jpg')>0)
//    {
//        $lowsrc=$html->find($_SESSION["img"],0)->alt;
//    }else{
//        $lowsrc2=$html->find($_SESSION["img"],0)->src;
//        $lowsrc=str_replace("/77x117/","/",$lowsrc2);
//        if($lowsrc=="")
//            $lowsrc=$html->find($_SESSION["img"],0)->href;
//    }
//
//    //------Seventeen--------------------
//    if($_SESSION["cat_id"]==47){
//        $lowsrc=str_replace("s_","_",$lowsrc);
//        $lowsrc=str_replace("s","",$lowsrc);
//        $lowsrc=str_replace("m_","_",$lowsrc);
//        $lowsrc=str_replace("m","",$lowsrc);
//        $lowsrc=str_replace("_h","_sh",$lowsrc);
//    }
//    //------Swirl by Swirl-MamaMia-Tutsi--------------------
//    if(($_SESSION["cat_id"]==3) || ($_SESSION["cat_id"]==5) || ($_SESSION["cat_id"]==14)){
//        $lowsrc=str_replace("135___195","___",$lowsrc);
//        $lowsrc=str_replace("330___515","___",$lowsrc);
//    }
//    //------Lenida--------------------
//    if($_SESSION["cat_id"]==16){
//        $lowsrc=str_replace("h595","h1000",$lowsrc);
//    }
//
//
//    //------Agio-Z-------------------
//    if($_SESSION["cat_id"]==45){
//        $lowsrc=str_replace(".JPG","_enl.JPG",$lowsrc);
//    }
//    //------S&L-------------------
//    if($_SESSION["cat_id"]==48){
//        $lowsrc=str_replace("smal/","/",$lowsrc);
//    }
//    //------SKHouse--------------------
//    if($_SESSION["cat_id"]==49){
//        //	$lowsrc=str_replace("200x300.jpg","550x825.jpg",$lowsrc);
//        $lowsrc=str_replace(".jpg.product.jpg",".jpg",$lowsrc);
//    }
//    //----OlisStyle-----------------
//    if($_SESSION["cat_id"]==58){
//        $lowsrc=str_replace("-70x81.jpg","-500x579.jpg",$lowsrc);
//    }
//    //------Majaly-Nelli_co-------------------
//    if($_SESSION["cat_id"]==65 || $_SESSION["cat_id"]==62){
//        $lowsrc=str_replace("w200_h200","w640_h640",$lowsrc);
//    }
    //----B1-----------------
    if($_SESSION["cat_id"]==64){
        $barr=array();
        $k=0;
        foreach($html->find('.color-selector li') as $a){
            $bpos=strpos($a->plaintext,"В наличии ");
            if($bpos!==false){
                //	echo $a->plaintext."<br/>";
                $barr[$k]=1;
            }else {
                $barr[$k]=0;
            }
            $k++;
        }

        $k=0;
        $bff=0;
        foreach($html->find('.color-selector li a') as $a){
            if($barr[$k]==1){
                $barr[$k]=$a->color;
                $bff=1;
            }
            $k++;
        }
        for($i=0; $i<count($barr); $i++){
            if($barr[$i]==true){
                $class=".class_".$barr[$i]." img";
                foreach($html->find($class) as $a){
                    //	echo $a->src."<br/>";
                    $bsrc=explode("/",$a->src);
                    $gf=$bsrc[count($bsrc)-1];
                    $p=strpos($gf,"-");
                    $bsub=substr($gf,0,$p);
                    $bsubb = '';
                    $bsubb.=$bsub."|";
                }
            }
        }
        $bsubb=substr($bsubb,0,strlen($bsubb)-1);

        if($bsubb==false)
            $bff=0;

        foreach($html->find('.prod-gallery a') as $a){
            $sa=explode("|",$bsubb);
            $aaa=strpos($a->href,$sa[0]);
            if($aaa!==false){
                $lowsrc=$a->href;
            }
        }
        if($bff==0){
            $queryNo2="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
            mysql_query($queryNo2) or die("error no_nal");
            echo "<br/><b style='color:red;' >Немає фото!!! Не опубликовать!</b>";
            return;
        }
    }
    //----FStyle-----------------
//    if($_SESSION["cat_id"]==63){
//        $lowsrc=str_replace("_2.png","_1.png",$lowsrc);
//        $lowsrc=str_replace("_3.png","_1.png",$lowsrc);
//        $lowsrc=str_replace("_4.png","_1.png",$lowsrc);
//        $lowsrc=str_replace("_5.png","_1.png",$lowsrc);
//    }
//
//    $eff=str_replace("http://","",$new_url);
//    //$eff=str_replace("http://","",$eff);
//    $adasda=explode("/",$eff);
//
//    $domain=array_shift($adasda);
//    $lowsrc=str_replace("http://","",$lowsrc);
//    $lowsrc=str_replace($domain,"",$lowsrc);
//
//    $src=$domain."/".$lowsrc;
//    $src=str_replace("//","/",$src);
//    $src="http://".$src;
//
//    $src=str_replace("majaly.com.ua/","",$src);
//    $src=str_replace("nelli-co.com/","",$src);
//    $src=rawurlencode($src);
//    $src=str_replace("%3A",":",$src);
//    $src=str_replace("%2F","/",$src);
//
//    $typeImg=explode(".",$src);
//    $type=$typeImg[count($typeImg)-1];
//
//    if($type=='png'){
//        $typeName=explode("/",$src);
//        $typeName2=$typeName[count($typeName)-1];
//        $typeName2=strstr($typeName2,'.',type);
//        $src=convert_image_type($type,'jpg',$src,$typeName2);
//    }
    echo "Url: <a href={$new_url} target='_blank'>{$new_url}</a><br/>";
//    echo "img: <a href={$src} target='_blank'>{$src}</a><br/>";
//    echo "Type Image: ".$typeName2.": ".$srcc."<br/>";

//----------Price-----------------------------------------------
    $price=$html->find($_SESSION["price"],0)->plaintext;//
    if($price=="") $price=$html->find($_SESSION["price"],0)->value;//
    $price=strip_tags($price);
    //------S&L--------------------
    if($_SESSION["cat_id"]==48){
        $price = strstr($price,"Розница");
    }
    //------Andrea Crocetta --------------------
    if($_SESSION["cat_id"]==63){
        $price=str_replace(" грн.","",$price);
        $price=str_replace("грн.","",$price);
        $pricee=strstr($price," ");
        if(intval($pricee)!=0){
            $price=strstr($price," ");
        }
    }
    $price = htmlentities($price, null, 'utf-8');
    $price=str_replace("&nbsp;","",$price);
    $price=str_replace(" ","",$price);
    $price=str_replace(",00","",$price);
    $price=str_replace(".00","",$price);
    $price=str_replace("грн.","",$price);
    $price=str_replace("Цена","",$price);
    $price=str_replace("цена","",$price);
    $price=str_replace(":","",$price);
    $price=str_replace("грн","",$price);
    $price=str_replace(",","",$price);
    $price=str_replace("Розница","",$price);
    $price=str_replace("Стоимость:","",$price);
    $price=(int)$price;
    //----Majaly-----------------
    if($_SESSION["cat_id"]==65){
        $excPrice=read_excel('../../excel/majaly.xls');
        $cod2=$cod;
        $num=strpos($cod,"№");
        if($num!==false)
            $cod2=str_replace("№","",$cod);
        foreach($excPrice as $val){
            $n=explode(",",$val[0]);
            if($n[0]==$cod2){
                $price=$val[1];
                $price=str_replace(".00","",$price);
                $selSizeMajay = $val[3];
                $selSizeMajay = str_replace(',', ';', $selSizeMajay);
            }
        }
        $num2=strpos($n[0],"№");
    }
    echo "<br/>Price: ".$price;
//=Price_Opt========================================
    $price2=$html->find($_SESSION["price2"],0)->plaintext;//
    if($price2=="") $price2=$html->find($_SESSION["price2"],0)->value;//
    $price2=strip_tags($price2);
    $price2 = htmlentities($price2, null, 'utf-8');
    $price2=str_replace("грн","",$price2);
    $price2=str_replace("&nbsp;","",$price2);
    $price2=str_replace("грн.","",$price2);
    $price2=str_replace(",","",$price2);
    $price2=str_replace("Розница","",$price2);
    $price2=str_replace("Опт ","",$price2);
    $price2=(int)$price2;


    //------SKHouse--------------------
    if($_SESSION["cat_id"]==49){
        $pri2=$html->find($_SESSION["price2"],1)->plaintext;
        if($pri2==true){
            $price2=$html->find($_SESSION["price2"],1)->plaintext;
            if($price2=="") $price2=$html->find($_SESSION["price2"],1)->value;
            $price2=strip_tags($price2);
        }
        $price2=substr($price2,0,strlen($price2)-2);
        $price2=str_replace(",","",$price2);
    }
    echo "<br/>Price2: ".$price2;
    if($_SESSION["per"]!=0)
    {
        $price2+=($price2/100)*$_SESSION["per"];
    }

//=Desc================================================
    $fs=0;
    $sid=0;
    $desc2=$html->find($_SESSION["desc"]);
    if(is_array($desc2))
    {
        foreach($desc2 as $value){
//            $sea=array("{$price}", "цена:","цена: ","цена :","Цена : ","Цена: ","Цена:","Наличие","Наличие:","грн.","грн",":  есть",":есть",": есть","<p>  </p>","<p>  &nbsp;</p>","<p>&nbsp;</p>");
            $desc3=str_replace($txt, "", $value);
            $desc = '';
            if(($_SESSION["cat_id"]!=3) && ($_SESSION["cat_id"]!=5) && ($_SESSION["cat_id"]!=14))
                $desc.=$desc3;
            if((($_SESSION["cat_id"]==3) || ($_SESSION["cat_id"]==5) || ($_SESSION["cat_id"]==14))&& $sid==0){
                $desc.=$desc3;
                $sid=1;
            }
        }
    }else
    {
        $desc=$desc2;
    }
    //---Fashioup-----------------------
    if($_SESSION["cat_id"]==2){
        $cod2="Модель: ".$cod;
        $desc=str_replace($cod, "", $desc);
        $desc=str_replace("t'ame", 't\'ame', $desc);
        $desc=str_replace("<span>Модель:</span>", "", $desc);
        $desc=str_replace('<div class="sttt"> </div>', '', $desc);
        $desc=str_replace('<div class="sttt">', '<div class=sttt >', $desc);
    }

    //------Swirl by Swirl-MamaMia-Tutsi--------------------
    if(($_SESSION["cat_id"]==3) || ($_SESSION["cat_id"]==5) || ($_SESSION["cat_id"]==14)){
        $desea=array("<p>","</p>","<span>","</span>","</div>",'<div class="right-tovar">','<span class="size">',"<ul>","</ul>");
        $desc=str_replace($desea,"",$desc);
        $desc=str_replace("Состав:".$cod,"",$desc);
        //	$desc=str_replace(,"",$desc);
        $desc=str_replace("<p></p>","",$desc);
        $desc=str_replace("<li>","<p>",$desc);
        $desc=str_replace("</li>","</p>",$desc);
        $col="";
        $color=$html->find('.cell',0)->plaintext;
        $pos=strpos($color,"Цвет:");
        if($pos!==false){
            $col="<p>".$color."</p>";
        }
        //echo "<br/>{$col}, {$color}<br/>";
        $desc.=$col;
    }
    //------Seventeen--------------------
    if($_SESSION["cat_id"]==47){

        $dtxt=array("<br />","<br/>","<br>","<div>","</div>",$txt2);
        $desc=str_replace($dtxt,"",$desc);
        //	$desc.="<p>";
        $dtxt=array('<div style="padding:30px 0px 0px 0px">',"Материал:","Длина","Цвет:","Внима","Празднич","Рукава","Воротник","ВНИМАН","Коктей","Плать","Исполь");
        $dtxt2=array('<div><p>',"</p><p>Материал:","</p><p>Длина","</p><p>Цвет:","</p><p>Внима","</p><p>Празднич","</p><p>Рукава","</p><p>Воротник","</p><p>ВНИМАН","</p><p>Коктей","</p><p>Плать","</p><p>Исполь");
        $desc=str_replace($dtxt,$dtxt2,$desc);
        //	$desc.="</p>";

        $dtxt2=array("<p></p>","<p><p>");
        $desc=str_replace($dtxt2,"",$desc);

        $spos=strpos($desc,"<a");
        $spos2=strpos($desc,"</a>");
        $subs=substr($desc, $spos, $spos2);
        $desc=str_replace($subs,"",$desc);
        $desc=str_replace('ь" class="fancybox-klose" onclick="document.getElementById(\'windrazmer\').style.display=\'none\'; return false;">',"",$desc);


        $rus2=strstr($desc,"Россий");
        $desc=str_replace($rus2,"",$desc);
        $desc=str_replace(" %26%2365533%3Bдный","",$desc);
        //echo "Pos: ".$rus2;

    }


    //-----Lenida--------------------------
    if($_SESSION["cat_id"]==16){
        $dtxt=array("<em style=\"line-height: 20.7999992370605px;\">","</em>","<em>","<em style=\"color: rgb(50, 25, 0); font-family: open_sanslight, sans-serif; font-size: 14px; line-height: 16px; background-color: rgb(253, 246, 234);\">");
        $descc=str_replace($dtxt,"",$desc);
        //	$desc.="<p>";
        $t=array('<div id="tabs-1" class="descr tab">',"Длин","Рост","Рекомендуемый","<br>","<br/>","Жакет","</div>",'Рост модели');
        $t2=array('<p><span class="sttt2">Описание: </span>',"</p><p>Длин","</p><p>Рост","</p><p>Рекомендуемый","","","</><p>Жакет","</p>",'<span class="sttt2">Рост модели</span>');
        $desc2=str_replace($t,$t2,$descc);
        $desc2.="2</p>";

        $dtxt2=array("<h3>","<p></p>","<p><p>","<p> </p>","<p>&nbsp;</p>","<p><i>&nbsp;</i></p>","<br/>","<br />","<br>","<h3>&nbsp;</h3>","<i>&nbsp;</i></p>");
        $desc=str_replace($dtxt2,"",$desc2);
        $desc=str_replace("</span> <p>","</span>",$desc);
        $desc=str_replace("<i>&nbsp;</i></p>","",$desc);
        $desc=str_replace("<h3>&nbsp;</h3>","",$desc);
        $desc=str_replace("</p>2</p>","",$desc);
        $desc=str_replace("Размер","</p><p>Размер",$desc);
        $desc=str_replace("Пояс","</p><p>Пояс",$desc);
        $desc=str_replace("2</p>","",$desc);

        //	$desc=str_replace('Приятных Вам покупок! С уважением, компания производитель модной женской одежды "LENIDA"',"",$desc);
        $str=strstr($desc, "<p>Приятных Вам покупок!");
        if($str==false)
            $str=strstr($desc, "Приятных Вам покупок!");
        $desc=str_replace($str,"",$desc);

        $sos=$html->find("#tabs-2",0)->plaintext;
        $sos1=$html->find('#tabs-1',0)->plaintext;
        $sos1=strip_tags($sos1);
        if($sos1==""){
            $desc=str_replace('<p><span class="sttt2">Описание: </span>',"",$desc);
            $sos=$html->find("#tabs-2",0)->plaintext;
        }else{
            echo "True";
        }

        $desc.="<p>Состав: ".$sos."</p>";
        $desc=str_replace("Состав:  Ткань","Ткань",$desc);

    }
    //------Sellin--------------------
    if($_SESSION["cat_id"]==23){
        $desc=str_replace('<div class="cpt_product_description"><div>','',$desc);
        $desc=str_replace("</div>","",$desc);
        $desc=str_replace("<p>Пальто женское</p>","",$desc);
        $r=0;
        $rr=0;
        while($rr==0){
            $sell=$html->find(".cpt_product_params_fixed table tr",$r)->plaintext;
            $fsell=strpos($sell,"Цвет");
            if($fsell!==false){
                $rr=1;
            }
            $r++;
        }
        $sell=str_replace("Цвет ","",$sell);
        $colSellin=$sell;
        $sel="Цвет: ".$sell;
        $desc.="<p>".$sel."</p>";
    }
    //------Meggi--------------------
    if($_SESSION["cat_id"]==42){
        $mp=strpos($desc,"<p>");
        $mp1=strpos($desc,"</p>");
        $d1=substr($desc,$mp,$mp1);
        $desc=str_replace($d1,"",$desc);
        $desc=str_replace("<ul>","",$desc);
        $desc=str_replace("</ul>","",$desc);
        $dtxt2=array("<span>","<p>","</p>");
        $dtxt3=array("<span class='sttt2'>","","");
        $desc=str_replace("<li>","",$desc);
        $col=$html->find('#colorselect',0)->plaintext;
        $desc.="<p>Цвет: {$col}</p>";
    }
    //------Agio-Z-------------------
    if($_SESSION["cat_id"]==45){
        $d2=array("Ткань:","Длина:");
        $d22=array("<span class=sttt2 >Ткань:</span>","<span class=sttt2 >Длина:</span>");
        $desc=str_replace($d2,$d22,$desc);
    }

    //------S&L-------------------
    if($_SESSION["cat_id"]==48){
        $d2=array("Длина :","Длина рукава:","Застёжка:","Ткань:","Длина пиджака:","Длина:","Потайная","Длина платья:","Пояс","Длина рубашки:","Длина юбки:","Застёжка:","Длина");
        $d22=array("</p><p><span class=sttt2 >Длина: </span>","</p><p><span class=sttt2 >Длина рукава:</span>","</p><p><span class=sttt2 >Застёжка:</span>","</p><p><span class=sttt2 >Ткань:</span>","</p><p><span class=sttt2 >Длина пиджака:</span>","</p><p>Длина:","</p><p>Потайная","</p><p>Длина платья:","</p><p>Пояс","</p><p>Длина рубашки:","</p><p>Длина юбки:","</p><p>Застёжка:","</p><p>Длина");
        $desc=str_replace($d2,$d22,$desc);
        $desc=str_replace("<p>  </p>","",$desc);
        $desc=str_replace("Состав:".$cod,"",$desc);
        $desc=str_replace("</p><p></p><p>","</p><p>",$desc);
        $desc=str_replace("</p><p><span class=sttt2 ></p><p>","</p><p>",$desc);
    }
    //------Alva-------------------
    if($_SESSION["cat_id"]==43){
        $d2=array($price,'<span style="color: rgb(72, 62, 59); font-family: \'Trebuchet MS\', Helvetica, Jamrul, sans-serif; font-size: 13px; line-height: 19px;">',"</span>");
        $ppa=strpos($desc, "Цена");
        if($ppa!==false){
            $stra=strstr($desc,"<p>  Цена");
            $aa=strpos($stra,"</p>");
            $stra=substr($stra,0,$aa);
            $desc=str_replace($stra."</p>","",$desc);
            //	echo $stra;
        }
        $ppa2=strpos($desc, "Наличие");
        if($ppa2!==false){
            $stra=strstr($desc,"<p>  Наличие");
            $aa=strpos($stra,"</p>");
            $stra=substr($stra,0,$aa+3);
            $desc=str_replace($stra."</p>","",$desc);
            //	echo $stra;
        }
        $desc=str_replace($d2,"",$desc);
        $desc=str_replace("<p>    </p>","",$desc);
        $desc=str_replace("<p>   </p>","",$desc);
        $desc=str_replace("<p>  </p>","",$desc);
        $desc=str_replace("Есть в наличии.","",$desc);
        $desc=str_replace("РАСПРОДАЖА","",$desc);
        $desc=str_replace("<p>  &nbsp;</p>","",$desc);
        $desc=str_replace('<span style="background-color:#ffd700;">',"",$desc);
        $desc=str_replace('</span>',"",$desc);
        $desc=str_replace('<strong>',"",$desc);
        $desc=str_replace('</strong>',"",$desc);
        $desc=str_replace('style="border: 0px; font-family: Arial, Helvetica, sans-serif; margin: 0px 0px 20px; padding: 0px; vertical-align: baseline; color: rgb(51, 51, 51); font-size: 14px;"',"",$desc);
        $desc=str_replace('<span style="border: 0px; font-family: \'Trebuchet MS\', Helvetica, Jamrul, sans-serif; margin: 0px; padding: 0px; vertical-align: baseline; color: rgb(72, 62, 59); font-size: 13px; line-height: 19px;">',"",$desc);
    }
    //-----FlFashion------------------
    if($_SESSION["cat_id"]==46){
        $desc=str_replace("<p>&nbsp;</p>","",$desc);
        $name2=str_replace('"',"",$name);
        $nam1=strstr($name2," ");
        $nam2=strstr($name2," ",true);
        $nam1=str_replace(" ","",$nam1);
        $nam2=str_replace(" ","",$nam2);
        if($nam1){
            $nampos=strpos($desc,$nam1);
        }else{
            $nampos=strpos($desc,$nam2);
        }

        if($nampos!==false){
            $desc=str_replace("«","",$desc);
            $desc=str_replace("»","",$desc);
            $desc=str_replace('"',"",$desc);

            $desc=str_replace($nam1,"",$desc);
            $desc=str_replace($nam2,"",$desc);
        }

        $desc=str_replace('<p>Комбинезон </p>',"",$desc);
        $desc=str_replace('Комбинезон "Амур"',"",$desc);
        $desc=str_replace('<p>Комбинезон-шорты «Бутон»</p>',"",$desc);
        $desc=str_replace('</h2>',"",$desc);
        $desc=str_replace('<h2>',"",$desc);
        $desc=str_replace('<span style="line-height: 1.5em;">',"",$desc);
        $desc=str_replace('<p> Санта - НОВИНКА ЭТОЙ ОСЕНИ!</p>',"",$desc);
        $desc=str_replace('Платье летнее ""',"",$desc);
        $desc=str_replace("Розничная цена: +70грн к цене на сайте","",$desc);

        $desc=str_replace("Розничная цена: +70 грн к цене на сайте","",$desc);
        $desc=str_replace("<h3><strong>Розничная цена +70грн к цене</strong></h3>","",$desc);
        $desc=str_replace("<h4><strong>СПЕЦИАЛЬНЫЕ УСЛОВИЯ ДЛЯ СП&nbsp; (подробности по запросу)</strong></h4>","",$desc);
        $desc=str_replace("<h4><strong>СПЕЦИАЛЬНЫЕ УСЛОВИЯ ДЛЯ СП  (подробности по запросу)</strong></h4>","",$desc);
        $desc=str_replace("<h4><strong>СПЕЦИАЛЬНЫЕ УСЛОВИЯ ДЛЯ СП&nbsp; (подробности по запросу)</strong></h4> ","",$desc);
        $desc=str_replace("<p>&nbsp;</p>","",$desc);
        $desc=str_replace("<p></p>","",$desc);
        $desc=str_replace("<br>","</p><p>",$desc);
        $desc=str_replace("<br />","",$desc);
        $desc=str_replace("<span>Розничная цена: +70 грн к цене на сайте</span>","",$desc);

        $fl=strpos($desc,"Розн");
        if($fl!==false){
            $sub1=strstr($desc,"Розн");
            $sub=strstr($sub1,"</p>",true);
            $desc=str_replace($sub,"",$desc);
        }
        $desc=str_replace("- это",$name." - это",$desc);
        $desc=str_replace("<p>.</p>","",$desc);
        $desc=str_replace("<p>. </p>","",$desc);
        $desc=str_replace("<p> .</p>","",$desc);
        $desc=str_replace("<p> </p>","",$desc);
        $desc=str_replace("<p>;</p>","",$desc);
        $desc=str_replace("<ul>  <li><em><span></p>","",$desc);
        $desc=str_replace("<ul>  <li><em><span style=line-height: 1.5em;></p>","",$desc);
        $desc=str_replace("<li><em><span style=line-height: 1.5em;>.</span></em></li>","",$desc);
        $desc=str_replace("<li>","<p>",$desc);
        $desc=str_replace("</li>","</p>",$desc);
        $desc=str_replace("<span style=line-height: 1.5em;>","",$desc);
        $desc=str_replace("<span>","",$desc);
        $desc=str_replace("</span>","</p>",$desc);
        $desc=str_replace("<em>","",$desc);
        $desc=str_replace("</em>","",$desc);
        $desc=str_replace("<ul>","",$desc);
        $desc=str_replace("</ul>","",$desc);
        $desc=str_replace("<h4><strong>СПЕЦИАЛЬНЫЕ УСЛОВИЯ ДЛЯ СП&nbsp; (подробности по запросу)</strong></h4>","",$desc);


    }
    //die();
    //------SKHouse--------------------
    if($_SESSION["cat_id"]==49){
        $desc=str_replace($desc[0].$desc[1].$desc[2].$desc[3].$desc[4],"<p>".$desc[0].$desc[1].$desc[2].$desc[3].$desc[4],$desc);

        $desc=str_replace('alt=""','',$desc);

        for($i=0; $i<60; $i++){
            if($i<10){
                $desc=str_replace('src="http://sk-house.ua/Images/data/blogs/0'.$i.'.png"','',$desc);
                $desc=str_replace('src="http://sk-house.ua/Images/data/blogs/Sings%2F0'.$i.'.png"','',$desc);
                $desc=str_replace('src="/Images/data/blogs/Sings%2F0'.$i.'.png"','',$desc);
                $desc=str_replace('src="/Images/data/blogs/0'.$i.'.png"','',$desc);
                $desc=str_replace('src="http://sk-house.ua/Images/data/blogs/image00'.$i.'.gif"','',$desc);
            }else{
                $desc=str_replace('src="/Images/data/blogs/Sings%2F'.$i.'.png"','',$desc);
                $desc=str_replace('src="http://sk-house.ua/Images/data/blogs/Sings%2F'.$i.'.png"','',$desc);
                $desc=str_replace('src="/Images/data/blogs/'.$i.'.png"','',$desc);
                $desc=str_replace('src="http://sk-house.ua/Images/data/blogs/'.$i.'.png"','',$desc);
                $desc=str_replace('src="http://sk-house.ua/Images/data/blogs/image0'.$i.'.gif"','',$desc);
            }
            $desc=str_replace('height="'.$i.'"','',$desc);
            $desc=str_replace('width="'.$i.'"','',$desc);
        }
        $im=strpos($desc,"<img");
        $im2=strpos($desc,'" />');
        $imga=substr($desc,$im,$im2);
        //	$desc=str_replace($imga,"",$desc);

        $desc=str_replace('title="чистка с использованием углеводорода, хлорного этилена, монофтортрихлорметана"','',$desc);
        $desc=str_replace('title="только ручная стирка, температура – 30 градусов"','',$desc);
        $desc=str_replace('title="нельзя отбеливать"','',$desc);
        $desc=str_replace('title="строго придерживаться указанной температуры, не подвергать сильной механической обработке, полоскать, переходя постепенно к холодной воде, при отжиме в стиральной машине, ставить медленный режим вращения центрифуги"','',$desc);
        $desc=str_replace('title="сушить при низкой температуре"','',$desc);
        $desc=str_replace('title="гладить при средней температуре (до 130 градусов)"','',$desc);
        $desc=str_replace('title="не выжимать,не сушить в стиральной машине"','',$desc);
        $desc=str_replace('title="температура воды 30 градусов"','',$desc);
        $desc=str_replace('title="гладить при низкой температуре (до 120 градусов)"','',$desc);
        $desc=str_replace('style="font-family:Calibri, sans-serif;font-size:14.6666669845581px;line-height:16.8666667938232px;"','',$desc);
        $desc=str_replace(' style="margin-bottom:0cm;margin-bottom:.0001pt;line-height:16.15pt;background:white;"','',$desc);
        $desc=str_replace('<img     />','',$desc);
        $desc=str_replace('<span style="font-family:tahoma, arial, verdana, sans-serif,','',$desc);
        $desc=str_replace(';font-size:11px;line-height:15.0699996948242px;">','',$desc);
        $desc=str_replace("'Lucida Sans'",'',$desc);
        $desc=str_replace('<span style="font-size:13.5pt;font-family:','',$desc);
        $desc=str_replace('<span style="font-size:11.5pt;font-family:','',$desc);
        $desc=str_replace(';color:black;">','',$desc);
        $desc=str_replace("'Times','serif'",'',$desc);
        $desc=str_replace("<strong>&nbsp;</strong>",'',$desc);
        $desc=str_replace('style="margin:0cm 0cm 12pt;"','',$desc);
        $desc=str_replace('<span style="font-family:Arial, sans-serif;"','',$desc);
        $desc=str_replace('style="margin-top:0cm;margin-right:0cm;margin-bottom:12.0pt;margin-left:0cm;"','',$desc);
        $desc=str_replace('<span style="font-size:11pt;font-family:','',$desc);
        $desc=str_replace(', sans-serif;">','',$desc);
        $desc=str_replace("'Calibri Light'",'',$desc);
        $desc=str_replace("<p >></p>",'',$desc);
        //	$desc=str_replace("<strong>",'',$desc);
        $desc=str_replace('src="/Images/data/blogs/Sings%2F04.png"','',$desc);

        //$desc=str_replace("'Lucida Sans'",'',$desc);
        $desc=str_replace("'Lucida Sans'",'',$desc);
        $desc=str_replace('<img     />','',$desc);
        $desc=str_replace('<img    />','',$desc);
        $ad=array("<span>","</span>",'<span lang="EN-US" style="font-size:11.0pt;line-height:115%;font-family:\'Calibri\',\'sans-serif\';">',
            '<img alt="" src="/Images/data/blogs/Sings%2F18.png" title="температура воды 30 градусов" />',
            '<img alt="" src="/Images/data/blogs/09.png" title="нельзя отбеливать" />',
            '<img alt="" src="/Images/data/blogs/Sings%2F06.png" title="сушить при низкой температуре" />',
            '<img alt="" src="/Images/data/blogs/Sings%2F23.png" title="гладить при средней температуре (до 130 градусов)" />',
            '<img alt="" src="/Images/data/blogs/Sings%2F04.png" title="не выжимать,не сушить в стиральной машине" />',
            "<br />",
            '<span style="font-size:11.0pt;line-height:115%;font-family:\'Calibri\',\'sans-serif\';">',
            '<img alt="" src="/Images/data/blogs/Sings%2F03.png" title="чистка с использованием углеводорода, хлорного этилена, монофтортрихлорметана" />',
            '<img alt="" src="/Images/data/blogs/Sings%2F22.png" title="гладить при низкой температуре (до 120 градусов)" />',
            '<span style="color:#444347;font-family:frizquadratacregular, Arial, Helvetica, sans-serif;font-size:14px;line-height:18px;">');
        $desc2=str_replace($ad,"",$desc);
        $ad2=array("S длина","M длина","L длинСостав:71166а","XL длина","</p></p>");
        $ad22=array("</p><p>S длина","</p><p>M длина","</p><p>L длина","</p><p>XL длина","</p>");
        $desc33=str_replace($ad2,$ad22,$desc2);
        $desc333=str_replace("X</p><p>L","</p><p>XL",$desc33);
        $desc=str_replace("</p></p>","</p>",$desc333);
        $desc=str_replace("<p></p>","",$desc);
        $desc=str_replace("<p><p>","<p>",$desc);
        //$desc=str_replace("35","Соста́в 35",$desc);

        $w=strpos($desc,"%");
        if($w!==false){
            $wa=strstr($desc,"%",true);
            $w2=strrpos($wa,"<p>");
            echo "<br>".strrpos($wa,"<p>")."-".$w;
            for($i=$w2; $i<$w; $i++){
                $pp.=$desc[$i];
            }
            $k=0;
            for($i=$w2; $i<$w; $i++){
                if($k==3){
                    $pp2.="<span class=sttt2 >Состав:</span> ";
                }
                $pp2.=$desc[$i];

                $k++;
            }
            $desc=str_replace($pp,$pp2,$desc);
        }

    }
    //----OlisStyle-----------------
    if($_SESSION["cat_id"]==58){
        $desc=strip_tags($desc);
        $desc=str_replace("Характеристика","",$desc);
        $desc=str_replace("Цвет","<p>Цвет:",$desc);
        $desc=str_replace("Размеры","</p><p>Размеры:",$desc);
        $desc.="</p>";

    }
    //----Majaly-----------------
    if($_SESSION["cat_id"]==65){

        $desc=str_replace('<p>  Цвет:     <i class="b-product-info__value-icon" style="background-image: url(http://images.ua.prom.st/18119382_w16_h16_images_1.jpg)"></i>   Разные цвета   </p>',"",$desc);
        $desc=str_replace(' <i class="b-product-info__value-icon" style="background-image: url(https://images.ua.prom.st/18119412_w16_h16_0000ff_sinij.png)"></i>',"",$desc);
        $desc=str_replace(' <i class="b-product-info__value-icon" style="background-image: url(https://images.ua.prom.st/38264938_w16_h16_12belyj.png)"></i>',"",$desc);
        $desc=str_replace('<tr> <th class="b-product-info__header" colspan="2">Основные</th> </tr>',"",$desc);
        $desc=str_replace('<span class="icon-help" id="product-attribute-0-9"> </span>',"",$desc);
        $desc=str_replace('</td> <td class="b-product-info__cell">',":",$desc);
        $desc=str_replace('<tr> <td class="b-product-info__cell">',"<p>",$desc);
        $desc=str_replace('</td> </tr>',"</p>",$desc);
        $desc=str_replace('<table class="b-product-info"> ',"",$desc);
        $desc=str_replace('</table>',"",$desc);
        $desc=str_replace('<span class="icon-help" id="product-attribute-0-13"> </span>',"",$desc);
        $desc=str_replace('  :',":",$desc);
        $desc=str_replace(' :',":",$desc);
        $desc=str_replace('<span class="b-product-info__value">',"",$desc);
        $desc=str_replace('<span class="icon-help" id="product-attribute-0-13">',"",$desc);
        $desc=str_replace('</span>',"",$desc);
        $desc=str_replace('<span class="icon-help" id="product-attribute-0-0">',"",$desc);
        $desc=str_replace(' <span class="icon-help" id="product-attribute-0-0"> </span>',"",$desc);
        $desc=str_replace('<tr> <td class="b-product-info__cell">  Производитель <span class="icon-help" id="product-attribute-0-0"> </span>  </td> <td class="b-product-info__cell">       Majaly   </td> </tr>',"",$desc);

        $desc=str_replace('<p> Минимальный заказ: Украина (5шт), Россия (10шт) </p>',"",$desc);
        $desc=str_replace('<p> Минимальный заказ: Украина (5шт), Россия (10шт) любых моделей и размеров </p>',"",$desc);
        $desc=str_replace('Основные атрибуты',"",$desc);
        $desc=str_replace('Дополнительные характеристики',"",$desc);

    }
    //-------Nelli_co----------------
    if($_SESSION['cat_id']==62){
        $desc=str_replace("<p><strong>Состав:</strong></p><p>","<p><span class=sttt2 >Состав: </span>",$desc);
        $ne=strpos($desc,"<p><strong>Цвет:");
        $ne2=strrpos($desc,"<strong>Размеры:</strong>");
        $txtne=substr($desc, $ne, $ne2);
        $desc=str_replace($txtne,"",$desc);

        $txtne=str_replace("<strong>","",$txtne);
        $txtne=str_replace("</strong>","",$txtne);
        $txtne=str_replace("</p><p>",", ",$txtne);
        $txtne=str_replace("Цвет:,","Цвет:",$txtne);
        $txtne=str_replace(", Размеры:,","</p><p> Размеры:",$txtne);

        $nes=strpos($desc,"%");
        $nes2=strrpos($desc,"%");
        $dnes=substr($desc,$nes,$nes2);
        $dnes2=str_replace("</p><p>"," ",$dnes);
        $desc=str_replace($dnes,$dnes2,$desc);
        $desc.=$txtne;
    }
    //-------FStyle----------------
    if($_SESSION['cat_id']==63){
        $desc=str_replace("<h4>Наличие : Есть на складе</h4>","",$desc);
        $desc=str_replace("<h3>Описание</h3>","",$desc);
        $desc=str_replace("<h3>","<p>",$desc);
        $desc=str_replace("</h3>","</p>",$desc);
        $desc=str_replace(' style="text-align: justify;"',"",$desc);
        $desc=str_replace("<p>Детали</p>  <p>","<p>Детали: ",$desc);
        $desc=str_replace("<p>Детали</p> <p>","<p>Детали: ",$desc);
        $desc=str_replace("<p> </p>","",$desc);
        $desc=str_replace("</div>","",$desc);

        $pf=strpos($desc,'<div class="ctext">');
        $pf2=strpos($desc,'<p>');
        $tf=substr($desc,$pf,$pf2);
        $desc=str_replace($tf,"",$desc);

        $af=strpos($desc,"<a");
        $af2=strpos($desc,'</a>');
        $ta=substr($desc,$af,$af2);
        $ta2=strip_tags($ta);
        $desc=str_replace($ta,$ta2,$desc);
        $desc=str_replace("Состав:","</p><p>Состав:",$desc);
        $desc.="</p>";
    }
    //----B1-----------------
    if($_SESSION["cat_id"]==64){
        $art=strpos($desc,"Art Millano");
        if($art!==false){
            $artCatID=66;
            echo "<br>Yes Art Millano<br>";
        }
        $artArr=array("CROMIA","CHIARUGI","EMFACI","Giorgio Armani","Montblanc","TAVECCHI","GIRONACCI","CERRUTI");
        for($i=0; $i<count($artArr); $i++){
            $art=strpos($desc,$artArr[$i]);
            if($art!==false){
                echo "<br/>RETURN!";
                return;
            }
        }
        $desc=str_replace('<ul class="params nobullet">',"",$desc);
        $desc=str_replace("</ul>","",$desc);
        $desc=str_replace("</li>","",$desc);
        $desc=str_replace("<li>","",$desc);
        $desc=str_replace("<span>Основные характеристики</span>","",$desc);
        $desc=str_replace("<span>","<span class=sttt2 >",$desc);
        $desc=str_replace("</span>",": </span>",$desc);
        $desc=str_replace("</span> <p>","</span>",$desc);
        $desc=str_replace("<span","<p><span",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> B1 </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> Art Millano </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> CROMIA </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> CHIARUGI </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> Giorgio Armani </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> Montblanc </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> TAVECCHI </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> GIRONACCI </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Бренд: </span> CERRUTI </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >Пол: </span> Женский </p>',"",$desc);
        $desc=str_replace('<p><span class=sttt2 >оперативная доставка: </span> Экспресс - отправка </p>',"",$desc);

        //-------Color and Size, Наличии-----------------------
        $nonal2=$html->find(".color-selector",0)->plaintext;

        //	echo $nonal."<br/>";
        $nonal2=str_replace("наличии","наличии.",$nonal2);
        $pushCol="<p><span class=sttt2 >Цвет:</span>";
        $col=explode(".",$nonal2);
        for($i=0; $i<count($col); $i++){
            $posCol=strpos($col[$i],"В наличии");
            if($posCol!==false)
                $pushCol.=", ".$col[$i];
        }
        $pushCol=str_replace("Цвет:</span>,","Цвет:</span>",$pushCol);
        $pushCol=str_replace(" В наличии","",$pushCol);
        if($pushCol==""){
            $n=1;
            echo "<br/>нет наличии";
        }else{
            //	echo $pushCol;
        }
        //------------------------------

        $tbeg=strstr($desc,"<p><span class=sttt2 >Цвет: </span>");
        if($tbeg){
            $beg=strpos($tbeg,"<p>");
            $beg1=strpos($tbeg,"<p>",1);
            $sub=substr($tbeg,$beg,$beg1);

            $desc=str_replace($sub,$pushCol."</p>",$desc);
        }else{
            $desc.=$pushCol."</p>";
        }
        //echo "col: ".$pushCol;
        if($name==$cod){
            $pp=strpos($desc,"Стиль");
            if($pp!==false){
                $namee=strstr($desc,'<p><span class=sttt2 >Стиль:');
                $namee=strstr($namee,"</p>",true);

                $desc=str_replace($namee."</p>","",$desc);
                $namee=str_replace("<p><span class=sttt2 >Стиль: </span> ","",$namee);
                $namee=str_replace("</p>","",$namee);
                $namee=ucfirst_mb($namee);
                $name=$namee;
                echo "Name: ".$name;
            }
        }else{
            $namee=strstr($desc,'<p><span class=sttt2 >Стиль:');
            $namee=strstr($namee,"</p>",true);
            $desc=str_replace($namee."</p>","",$desc);
        }
    }
    //----Sergio Torri-----------------
    if($_SESSION["cat_id"]==85){
        $desc=str_replace('<div class="product-short-description">',"<p>",$desc);
        $desc=str_replace("<br />","</p><p>",$desc);
        $desc=str_replace("</div>","</p>",$desc);
    }
    echo $desc;
//8    $query2="UPDATE `shop_commodity` SET `com_fulldesc`='{$desc}' WHERE `from_url`='{$new_url}';";
    //		mysql_query($query2);
    //		mysql_query($query2) or die("Error desc text");
    //	echo $query2;

    //die();
    //echo $_SESSION["price"];die();
//===================Нет в наличии=================================
    $nonal=$html->find($_SESSION["no_nal"],0)->plaintext;
    $n=0;
    //------Fashioup------------
    if($_SESSION["cat_id"]==2){
        switch($nonal) {
            case 'Нет в наличии':
                $n=1;
                break;
            case 'Есть в наличии':
                $n=0;
                break;
            case 'Ограниченное количество':
                $n=0;
                break;
        }
    }
    if($_SESSION["cat_id"]==47){
        $sn=strpos($nonal,"Нет в наличии!");
        if($sn!==false){
            $n=1;
        }
    }
    if($_SESSION["cat_id"]==49){
        $sn=strpos($nonal,"Нет в наличии");
        if($sn!==false){
            $n=1;
        }
        $orn=$html->find('.color',0)->plaintext;
        $orn2=strpos($orn,"Цвет");
        if($orn2===false){
            $n=1;
        }else{
            echo "yes";
        }

    }
    if($_SESSION["cat_id"]==48){
        $sn=strpos($nonal,"нет на складе");
        if($sn!==false){
            $n=1;
        }
    }

    //echo "<br>{$nonal}";
//	die();
//=====================================================

//=Наценка===============================
    if($_SESSION["per"]!=0)
    {
        $plus=strpos($_SESSION["per"],'+');
        $minus=strpos($_SESSION["per"],'-');
        $p=strpos($_SESSION["per"],'%');
        $dil=strpos($_SESSION["per"],'/');
    }
    //------Nelli_co------------------
    if($_SESSION["cat_id"]==62){
        if($price2==0){
            $price2=$price/$_SESSION["per"];
        }
    }

    if(($_SESSION["cat_id"]==2) || ($_SESSION["cat_id"]==3) || ($_SESSION["cat_id"]==5) || ($_SESSION["cat_id"]==14)){
        $price2=$price/$_SESSION["per"];

    }
//    echo "<br/>Opt2: {$price2}<br/>";

    //------Meggi--------------------
    //	if($_SESSION["cat_id"]==42){
    //		$price2=$price;
    /*$query="
        UPDATE `shop_commodity`
        SET
        `commodity_price`='{$price}',
        `commodity_price2`='{$price2}'
        WHERE  `from_url`='{$new_url}'
        ;";

        mysql_query($query);*/
    //	}

    //------OlisStyle------------------
    if($_SESSION["cat_id"]==58){
        $per=$_SESSION["per"];
        $per=str_replace("*","",$per);
        $price2=$price;
        $price=$price2*$per+5;
    }
    //------Agio-Z--Seventeen--FlFashion----------------
    if(($_SESSION["cat_id"]==45) || $_SESSION["cat_id"]==47 || $_SESSION["cat_id"]==46){
        $per=$_SESSION["per"];
        $price2=$price;
        $price+=$per;

    }
    //------Lenida--Meggi--Majaly--------------
    if(($_SESSION["cat_id"]==16) || ($_SESSION["cat_id"]==42) || ($_SESSION["cat_id"]==43) || ($_SESSION["cat_id"]==65)){
        $price2=$price;
        $per=$_SESSION["per"];
        $sea=array("+","%");
        $per2=str_replace($sea,"",$per);

        $price=($price/100)*$per+$price2;

    }
    //-----B1-------------
    if($_SESSION["cat_id"]==64 || $_SESSION["cat_id"]==66){
        $price2=$price;
    }
    $query="UPDATE `shop_commodity` SET `commodity_price`='{$price}', `commodity_price2`='{$price2}' WHERE  `from_url`='{$new_url}';";
    mysql_query($query) or die("Error price");
    //	die();
    echo "Per: ".$_SESSION["per"]."<br/>";
    echo "Price: {$price}";
    echo "<br/>Opt: {$price2}<br/>";

//------------------------------------------------------
//------------------------------------------------------
    $q=mysql_query("SELECT * FROM `shop_commodity`") or die("Error select");

    //===========Search========================

    for($i=0; $i<mysql_num_rows($q); $i++){
        $f = mysql_fetch_array($q);
        if($new_url==$f['from_url']){
            //	echo "ComId: ".$f['commodity_ID']."<br/>";
            $comid=$f['commodity_ID'];
        }
    }
//------S&L select color and size------------------------
    $selColSize="";
    $selSize2=array();

    $selSize=$html->find($_SESSION["sizeCol"]);
    //--------Fashioup--Meggi-FStyle-Seventeen-flfashion-----------------
    if($_SESSION["cat_id"]==1 || $_SESSION["cat_id"]==2 || $_SESSION["cat_id"]==42 || $_SESSION["cat_id"]==46 || $_SESSION["cat_id"]==47 || $_SESSION["cat_id"]==63 || $_SESSION["cat_id"]==65){
        foreach($selSize as $key=>$a){
            $selSize2[$key].=$a->plaintext;
        }
        $selSize=implode(";", $selSize2);
        $selSize=str_replace("Без пояса","",$selSize);
        $selSize=str_replace("С поясом (+25грн.)","",$selSize);
        $selSize=str_replace(";;","",$selSize);
    }
    //--------B1-------------------
    if($_SESSION["cat_id"]==64){
        $selSize="";
        //$query33="UPDATE `shop_commodity` SET `com_sizes`='', `select_color`=''  WHERE  `from_url`='{$new_url}';";
        //mysql_query($query33) or die("Error select2");
        //		foreach($selSize as $key=>$a){
        //			$selSize2[$key].=$a->plaintext;
        //		}
        //		$selSize=implode(";", $selSize2);
        //		echo "S: ".$selSize->plaintext;
        $pp=strpos($desc,"Цвет:");
        if($pp!=false){
            $selB=strstr($desc,"Цвет:");
            $posB=strpos($selB,"</p>");
            $subb=substr($selB,0,$posB);
            $subb=str_replace("Цвет: ","",$subb);
            $subb=str_replace("Цвет:","",$subb);
        }else{
            $selB=strstr($desc,"Цвет");
            $posB=strpos($selB,"</p>");
            $subb=substr($selB,0,$posB);
            $subb=str_replace("Цвет фурнитуры: ","",$subb);
            $subb=str_replace("Цвет фурнитуры:","",$subb);
        }
        $subb=str_replace(", ","|",$subb);
        $subb=str_replace(",","|",$subb);

        $subb2=explode("|",$subb);
        $selColSize.="<select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
        if(is_array($subb2)){
            //echo 'max2'.count($subb2);
            for($i=0; $i<count($subb2); $i++){
                $selColSize.='<option value="'.$subb2[$i].'" >'.ucfirst_mb($subb2[$i]).'</option>';
            }
        }
        $selColSize.="<select>";
        //$selColSize=$subb;
        //echo $subb;
    }
    //-------SK-House-------------------
    if($_SESSION["cat_id"]==49){

        $selColSize.="<select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
        foreach($selSize as $key=>$a){
            if($a->plaintext!="-- Цвет --")
                $selColSize.='<option value="'.$a->plaintext.'" >'.ucfirst_mb($a->plaintext).'</option>';
        }
        //$selSize=implode(";", $selSize2);
        $selColSize.="<select>";
        $selSize="";
        $selSize2=$html->find('#SizeId option');
        $selColSize.="<select id=id_choos_{$comid} class=cl_choos rel={$comid}>";
        foreach($selSize2 as $key=>$a){
            if($a->plaintext!="-- Размер --"){
                $selColSize.='<option value="'.$comid.';'.$a->plaintext.'" >'.$a->plaintext.'</option>';
                $selSizeSK.=$a->plaintext.";";
            }
        }
        $selColSize.="<select>";
        $selSize=substr($selSizeSK,0,strlen($selSizeSK)-1);
    }
    //--------olis-style------------------
    if($_SESSION["cat_id"]==58){
        foreach($selSize as $key=>$a){
            $selSize2[$key].=$a->plaintext;
            $selSize2[$key]=str_replace(" --- Выберите --- ","Размер",$selSize2[$key]);
        }
        $selSize=implode(";", $selSize2);
        //	$selSize=substr($selSizeSK,0,strlen($selSizeSK)-1);
    }
    //--------Alva------------------
    if($_SESSION["cat_id"]==43){
        foreach($selSize as $a){
            if($a->plaintext>20)
                $selll.=$a->plaintext.";";
            //	echo "s: ".$a->plaintext;
        }

        $acol = gggColSize($comid,32);
        //$aa=gggColSize($comid,27);
        if($acol==true){
            //---Color----
            $ass22=explode("|", $acol);
            $selColSize.="<select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
            for($i=0; $i<count($ass22); $i++){
                if($ass22[$i]!=false)
                    $selColSize.='<option value="'.$ass22[$i].'" >'.$ass22[$i].'</option>';
            }
            $selColSize.="<select>";
            //---Size---
            $acol2=explode(";",$selll);
            $selColSize.="<br><select id=id_choos_{$comid} class=cl_choos rel={$comid}>";
            for($i=0; $i<count($acol2); $i++){
                if($acol2[$i]!=false)
                    $selColSize.='<option value="'.$comid.';'.$acol2[$i].'" >'.$acol2[$i].'</option>';
            }
            $selColSize.="<select>";

        }
        //$ass=explode("|", $aa);
        //$ass2=implode(";",$ass);
        //$ass2=substr($ass2,0,strlen($ass2)-1);
        //echo $ass2;
        $selll=str_replace(" ","",$selll);
        $selll=str_replace("-;","",$selll);
        $selll=str_replace("-","",$selll);
        $selll=substr($selll,0,strlen($selll)-1);
        $selSize=$selll;
    }
    //--------agio-z------------------
    if($_SESSION["cat_id"]==45){
        $selSize="";
        $selSize2="";
        $tableTrs = $html->find('.cpt_product_params_selectable tr');
        foreach($tableTrs as $key=>$trs) {
            if (preg_match('/Размер/',$trs->plaintext)) {
                $nth = $key + 1;
                $selectorSizee =  "select[name='option_{$nth}'] option";
            } elseif (preg_match('/Цвет/',$trs->plaintext)) {
                $nth1 = $key + 1;
                $selectorColor =  "select[name='option_{$nth1}'] option";
            }
        }
        $selSizee=$html->find($selectorSizee);
        $txt22.= "<br><select id=id_choos_{$comid} class=cl_choos rel={$comid}>";
        foreach($selSizee as $a){
            $selSize2.=$a->plaintext.";";
            if($a->plaintext!="Не определено")
                $txt22.='<option value="'.$comid.";".$a->plaintext.'" >'.$a->plaintext.'</option>';
        }
        $txt22.="</select>";
        $selSize2=str_replace("Не определено;","",$selSize2);
        $selSize2=str_replace(" ","",$selSize2);
        $selSize2=substr($selSize2,0,strlen($selSize2)-1);
        //echo $selSize2;
         $checktxt=strip_tags($txt22);
        if($checktxt==""){
            $txt22 = "";
            $ppo=strpos($desc,"Размер");
            if($ppo!=false){
                $st=strstr($desc,"Размер");
                $ppo2=strpos($st,"</p>");
                $selSize2=substr($st,0,$ppo2);
                $selSize2=str_replace("Размер:","",$selSize2);
                $mass = explode(',', $selSize2);
                $selSize2 = implode(';', $mass);
                $txt22.= "<br><select id=id_choos_{$comid} class=cl_choos rel={$comid}>";
                foreach($mass as $a){
                        $txt22.='<option value="'.$comid.";".$a.'" >'.$a.'</option>';
                }
                $txt22.="</select>";
            }
        }

        //-----Select Size and Color------
        $selSize3="";
        $selSizee=$html->find($selectorColor);
        $select="<select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
        foreach($selSizee as $a){
            $selSize3.=$a->plaintext.";";
            if($a->plaintext!="Не определено")
                $select.='<option value="'.$a->plaintext.'" >'.$a->plaintext.'</option>';
        }
        $select.="</select>";
        $checksel=strip_tags($select);
        if($checksel==""){
            $select="";
        }
        
        $selColSize=$select.$txt22;
        $checksell=strip_tags($selColSize);
        if($checksell==""){
            $selColSize="";
        }
        $selSize=$selSize2;
    }
    //-------- Andrea Crocetta --------------------
    //if($_SESSION["cat_id"]==63){
    //	echo "S:".$selSize->innertext;
    //}
    //--------Lenida---------------------
    if($_SESSION["cat_id"]==16){
        foreach($selSize as $e){
            if($e->plaintext!=false)
                $selSizeL.=$e->plaintext.";";
        }
        $selSize=substr($selSizeL,0,strlen($selSizeL)-1);

    }

    //-------SwirlBySwirly-MamaMia-Tutsi---------------------------
    if(($_SESSION["cat_id"]==3) || ($_SESSION["cat_id"]==5) || ($_SESSION["cat_id"]==14)){
       /* $sel=strstr($desc,"<p>Размеры:");
        $spos=strpos($sel,"<p>");
        $spos2=strpos($sel,"</p>");
        $subs=substr($sel,$spos,$spos2);
        $subs=str_replace("<p>","",$subs);
        $subs=str_replace("</p>","",$subs);
        $subs=str_replace("Размеры: ","",$subs);
        $arr=explode(" ",$subs);
        //$txt22.= "<select id=id_choos_{$comid} class=cl_choos rel={$comid}><option value={$comid} >Размер</option>";
        for($i=0; $i<count($arr); $i++){
            if($arr[$i]!=false)
                //	$txt22.='<option value="'.$comid.";".$arr[$i].'" >'.$arr[$i].'</option>';
                $txt22.=$arr[$i].";";
        }*/
        //$txt22.="</select>";
        //echo $txt22;
        $selColSize="";
       // $selSize=$selSize=substr($txt22,0,strlen($txt22)-1);
         if ($_SESSION["cat_id"]==14 || ($_SESSION["cat_id"]==5) || $_SESSION["cat_id"]==3) {
            $selSize = "";
            $element = $html->find("li span.size");
            $arr = explode (" ", $element[0]->plaintext);
            foreach ($arr as $sSize) {
                if ($sSize != ""){
                    $selSize .= $sSize.';';
                }
            }
            $selSize=substr($selSize,0,strlen($selSize)-1);
        }
    }
    //--------Sellin----------------------
    if($_SESSION["cat_id"]==23){
        $colarr=explode(",",$colSellin);
        echo $colarr[1];
        $txt22="";
        $txt22.= "<br/><select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
        for($i=0; $i<count($colarr); $i++){
            $colarr[$i]=str_replace("Цвет: ","",$colarr[$i]);
            $txt22.='<option value="'.$colarr[$i].'" >'.$colarr[$i].'</option>';
        }
        $txt22.="</select>";
        $txt22.= "<br/><select id=id_choos_{$comid} class=cl_choos rel={$comid}>";
        foreach($selSize as $key=>$a){
            $sel=intval($a->plaintext);
            if($sel!=0){
                $selSize2[$key].=$sel;
                $selSize2[$key]=str_replace(" ","",$selSize2[$key]);
                $txt22.='<option value="'.$comid.";".$selSize2[$key].'" >'.$selSize2[$key].'</option>';
                $s[$key]=$selSize2[$key];
            }
        }
        $txt22.="</select>";
        //	$selSize=implode(";", $selSize2);
        echo "Size".$colSellin;
        $selColSize=$txt22;
        $selSize=implode(";", $s);
    }
    //--------Meggi----------------------
    /*	if($_SESSION["cat_id"]==42){
            foreach($selSize as $key=>$a){
                $selSize2[$key].=$a->plaintext;
            }
            $selSize=implode(";", $selSize2);
        }*/

    //--------nelli-co----------------------
    if($_SESSION["cat_id"]==62){
        $sen=strstr($desc,"Размер");
        $pn=strpos($sen,"</p>");
        $selSize=substr($sen,0,$pn);
        $selSize=str_replace(" ",";",$selSize);
        $selSize=str_replace(",",";",$selSize);
        $selSize=str_replace(";;",";",$selSize);
        $selSize=str_replace("Размер;","",$selSize);
        $selSize=str_replace("Размеры:;","",$selSize);
    }
    //--------Majaly----------------------
    if($_SESSION["cat_id"]==65){
        $posm=strpos($desc,"Размер:");
        if($posm!==false){
            $strm=strstr($desc,"Размер");
            $posm2=strpos($strm,"</p>");
            $subm=substr($strm,0,$posm2-1);
            $subm=str_replace(" ","",$subm);
            $subm=str_replace("Размер:","",$subm);
            $subm=str_replace(",",";",$subm);
            //	echo $subm;
        }

        $posm2=strpos($desc,"Цвета:");
        if($posm2===false){
            $strm2=strstr($desc,"Цвет:");
            $posm22=strpos($strm2,"</p>");
            $subm2=substr($strm2,0,$posm22-1);
            //$subm2=str_replace(" ","",$subm2);
            $subm2=str_replace("Цвета:","",$subm2);
            $subm2=str_replace("Цвет:","",$subm2);
            $subm2=str_replace("Как на фото","",$subm2);
            $subm2=str_replace("Разные цвета","",$subm2);
            $subm2=str_replace("Вставка: ","",$subm2);
            $subm2=str_replace("Вискоза: ","",$subm2);
            $subm2=str_replace(",",";",$subm2);
        }else{
            $strm2=strstr($desc,"Цвета:");
            $posm22=strpos($strm2,"</p>");
            $subm2=substr($strm2,0,$posm22-1);
            //$subm2=str_replace(" ","",$subm2);
            $subm2=str_replace("Цвета:","",$subm2);
            $subm2=str_replace("Цвет:","",$subm2);
            $subm2=str_replace("Как на фото","",$subm2);
            $subm2=str_replace("Вставка: ","",$subm2);
            $subm2=str_replace("Вискоза: ","",$subm2);
            $subm2=str_replace(",",";",$subm2);
            //	echo $subm2;
        }
        if($subm2!=false){
            $arrcol=explode(";", $subm2);
            $txt22.= "<select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
            for($i=0; $i<count($arrcol); $i++){
                $arrcol[$i]=strip_tags($arrcol[$i]);
                $txt22.='<option value='.$arrcol[$i].' >'.$arrcol[$i].'</option>';
                //echo "i:".$arrcol[$i];
            }
            $txt22.="</select>";
        }
        if($subm!=false){
            $arrsize=explode(";", $subm);
            $txt22.= "<br/><select id=id_choos_{$comid} class=cl_choos rel={$comid}>";
            for($i=0; $i<count($arrsize); $i++){
                $arrsize[$i]=strip_tags($arrsize[$i]);
                $txt22.='<option value="'.$comid.";".$arrsize[$i].'" >'.$arrsize[$i].'</option>';
            }
            $txt22.="</select>";
        }
//        $mm=strip_tags($txt22);
        $mmm=strip_tags($subm2);
        $mmm=str_replace(" ","",$mmm);
        if($mm="" || $mmm==""){
            $selColSize="";
        }else {
            $selColSize=$txt22;
        }
        //$selSize=$subm;
        $selSize=$selSizeMajay;
    }
    //--------Sergio Torri----------------------
    if($_SESSION["cat_id"]==85){
        //	echo "wwwww";
        $selSize="";
        $selColSize="";
        $query="UPDATE `shop_commodity` SET `com_sizes`='', `select_color`=''  WHERE  `from_url`='{$new_url}';";
        mysql_query($query) or die("Error select2");
    }
    if($selSize!=""){
        echo "Size: ".$selSize."<br>";
        $query="UPDATE `shop_commodity` SET `com_sizes`='{$selSize}',`select_color`=''  WHERE  `from_url`='{$new_url}';";
        mysql_query($query) or die("Error select2");
    }
    if($selColSize!=""){
        echo "SizeColor: <br>".$selColSize;
        $query="UPDATE `shop_commodity` SET `select_color`='{$selColSize}'  WHERE  `from_url`='{$new_url}';";
        mysql_query($query) or die("Error select2");
    }

//---S&L---------------
    if($_SESSION["cat_id"]==48){
        $selColSize="";
        echo "<br/>ComId: ".$comid;
        $txtt2="";
        $getTxt="";
        $getCol="";
        $h=file_get_html($new_url);
        foreach($h->find("form table b") as $e){
            $color=$e->innertext;
            $color=str_replace("Размер","",$color);
            $color=preg_replace("/\d/","",$color);
            if($color!=false)
                $getCol.=$color."|";
        }
        if($getCol==""){
            $querysel="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `commodity_ID`='{$comid}';";
            mysql_query($querysel) or die("error no_nal");
            echo "Нет наличии 0";
        }
        //	echo "<br/>2:".$getCol;
        echo "<br/>";
        foreach($h ->find('form table div') as $e){
            $txt=$e->innertext;
            $pos=strpos($txt,'<ul class="left_menu">');
            $pos2=strpos($txt,"</script>");
            $txtt2=substr($txt,$pos,$pos2);
            $txt=str_replace($txtt2,"",$txt);

            $sel=strpos($txt,"<select");
            $sel2=strpos($txt,"</select>");
            $txt22=substr($txt,$sel,$sel2);
            $txt=str_replace($txt22,"",$txt);
            $txt=str_replace("<br>","",$txt);
            $txt=str_replace("</br>","",$txt);
            $txt=str_replace("<br/>","",$txt);
            $txt=str_replace(">","",$txt);
            $min=strpos($txt,"-");
            if($min!==false){
                $begin=substr($txt,0,$min);
                $end=substr($txt,$min+1,strlen($txt));
                $begInt=intval($begin);
                $endInt=intval($end);
                $uptxt="";
                while($begInt<=$endInt){
                    $uptxt.=$begInt."|";
                    $begInt+=2;
                }
                $txt=$uptxt;
            }
            if($txt!=false)
                $getTxt.=$txt."|";
            $getTxt=str_replace("||","|",$getTxt);
            $getTxt=str_replace(" ","",$getTxt);
        }


        //echo $getCol."<br/>";
        $setCol=explode("|",$getCol);
        $txtt2.= "<select id=id_choos2_{$comid} class=cl_choos2 rel={$comid}>";
        for($i=0; $i<count($setCol); $i++){
            if($setCol[$i]!=false)
                $txtt2.='<option onclick=sel('.$i.') value="'.$setCol[$i].'" >'.$setCol[$i].'</option>';
        }
        $txtt2.="</select><br/><div id=seltxt></div>";


        $j=0;
        $k=0;

        $setTxt=explode("|",$getTxt);
        for($i=0; $i<count($setTxt); $i++){
            $f=0;
            if($setTxt[$i]!=false){
                $mulCol[$j][$k]=$setTxt[$i];
                if($setTxt[$i]>=$setTxt[$i+1]){
                    $j++;
                    $k=0;
                    $f=1;
                }
            }
            if($f==0){
                $k++;
            }
        }

        $ssel="";
        for($i=0; $i<count($setCol); $i++){
            if($setCol[$i]!=false){
                $ssel.=$setCol[$i]."=";
                for($j=0; $j<count($mulCol[$i]); $j++){
                    $ssel.=$mulCol[$i][$j].",";
                }
                $ssel.=";";
            }
        }
        $ssel=str_replace(",;",";",$ssel);
        $ssel=substr($ssel,0,strlen($ssel)-1);
        echo $ssel;
        $query="UPDATE `shop_commodity` SET `commodity_select`='{$ssel}' WHERE  `from_url`='{$new_url}';";
        mysql_query($query) or die("Error select2");
    }

//	die();

    //--------------------------------------------------------------
    // B1 with brenda
    //--------------------------------------------------------------
    if($_SESSION["cat_id"]==64){
        $query="INSERT INTO `shop_commodities-categories` SET `commodityID`='{$comid}', `categoryID`='{$_SESSION["cat_id"]}';";
        mysql_query($query);

        if($artCatID==66){
            $query="INSERT INTO `shop_commodities-categories` SET `commodityID`='{$comid}', `categoryID`='{$artCatID}';";
            mysql_query($query);
            echo "<br/>CatId: ".$artCatID;

            $delCat="DELETE FROM `shop_commodities-categories` WHERE `commodityID`='{$comid}' AND `categoryID`='{$_SESSION["cat_id"]}'; ";
            mysql_query($delCat);
        }
    }

    $catid=$_SESSION["cat_id"];
    if($artCatID!=0){
        $catid=$artCatID;
    }

    $sql="SELECT * FROM `shop_commodity`
		WHERE  `from_url`='{$new_url}';";
    $row=mysql_fetch_assoc(mysql_query($sql));
    if($row)
    {
        if ($catid == 14||$catid == 3||$catid == 5) {
            $pattern = '/Цвет:([\S]*)[\s]?<\/p>/';
            preg_match($pattern, $desc, $coincidence);
            preg_match($pattern, $row['com_fulldesc'], $coincidence1);
            if ($coincidence[1] != $coincidence1[1]) {
                $queryNo2="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
                mysql_query($queryNo2) or die("error no_nal");
                echo "<br/><b style='color:red;' >Не опубликовать! (MamaMia, Tutsi, SwirlBySwirl - разний цвет!)</b>";
                return;
            }
        }
        $query="
			INSERT INTO `shop_commodities-categories`
			SET `commodityID`='{$row["com_id"]}', `categoryID`='{$catid}';";
        @mysql_query($query);
//
//        $query="
//					UPDATE `shop_commodity`
//					SET
//					`commodity_price`='{$price}',
//					`commodity_old_price`='{$old_price}',
//					WHERE  `from_url`='{$new_url}'
//					;";
        if($price<=10){
            $n=1;
            echo "<br/><b style='color:red;' >Немає ціна!</b>";
        }

//        if($src=="http://www.lenida.com.ua/"){
//            $n=1;
//            echo "<br/><b style='color:red;' >Немає фото!!!</b>";
//        }
  //      mysql_query($query);
        $f=ggg($comid);
        if($f==false){
            $n=1;
            $query2="UPDATE `shop_commodity` SET `commodity_order`='0' WHERE `from_url`='{$new_url}';";
            mysql_query($query2) or die("no tegi");
            echo "<br>Немає теги";
        }else{
            //	echo $comid."Tegi: ".$f;
            $query2="UPDATE `shop_commodity` SET `commodity_visible`='1' WHERE `from_url`='{$new_url}';";
            mysql_query($query2) or die("error");
        }
        if($n==1){
            $queryNo2="UPDATE `shop_commodity` SET `commodity_visible`='0' WHERE `from_url`='{$new_url}';";
            mysql_query($queryNo2) or die("error no_nal");
            echo "<br/><b style='color:red;' >Не опубликовать!</b>";
        }
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



    usleep(200000);
    if($_SESSION["cat_id"]==42) {
        sleep(1);
    }
    echo "<hr>";   
    $content = ob_get_contents();
    file_put_contents($_SESSION['filename'], $content, FILE_APPEND);
    ob_flush();
    ob_end_clean();
$return = [$content, $updated];
    return $return;
}
