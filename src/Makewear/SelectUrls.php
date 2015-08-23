<?php
/**
 * Created by PhpStorm.
 * User: volodini
 * Date: 6/20/15
 * Time: 3:14 PM
 */
namespace Makewear;

use PDO;

class SelectUrls
{
    const DSN = 'mysql:dbname=zoond_make;host=217.12.201.205';
    const USER = 'zoond_make_r';
    const PASSWORD = 'makewear12';
//    const DSN = 'mysql:dbname=makewear;host=127.0.0.1';
//    const USER = 'root';
//    const PASSWORD = '123123q';
    const MSG_SUCCESS = "Не отображать!";
    const MSG_NOT_SUCCESS = "Не удалось скрить товaр!";
    const MSG_SUCC_SIZE = "Размер обновился!";
    const MSG_NOT_SUCC_SIZE = "Размер не обновился!";
    const MSG_SUCC_QUANT = "Количество записалось!";
    const MSG_NOT_SUCC_QUANT = "Количество не записалось!";
    const MSG_SUCC_QUANT_UP = "Количество обновилось!";
    const MSG_NOT_SUCC_QUANT_UP = "Количество не обновилось!";

    private $dbh;

    private $urls = array();

    private $quantities;

    private $allSizes;

    private $allOptPrices;

    public function __construct()
    {
        $this->dbh = new \PDO(self::DSN, self::USER, self::PASSWORD);
    }

    public function getUrls()
    {
        $this->quantities = $this->getAllQuantity();
        $sql = "SELECT * FROM `shop_commodity` as `item`
INNER JOIN `shop_commodities-categories` as `cat`
ON item.commodity_ID = cat.commodityID
WHERE cat.categoryID = 1 AND item.commodity_visible = 1";
        $stmt = $this->dbh->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['commodity_ID'];
            $this->urls[$id] = $row['from_url'];
            $this->allSizes[$id] = $row['com_sizes'];
            $this->allOptPrices[$id] = $row['commodity_price2'];
        }
        return $this->urls;
    }

    public function hideItem($id)
    {
        $sql = "UPDATE `shop_commodity` SET `commodity_visible`=0
WHERE `commodity_ID` = $id";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            return self::MSG_SUCCESS;
        } else {
            $this->dbh = new \PDO(self::DSN, self::USER, self::PASSWORD);
            echo $this->dbh->exec($sql);
            return self::MSG_NOT_SUCCESS;
        }
    }

    public function updateSize($id, $sizes)
    {
        $sizes = $this->dbh->quote($sizes);
        $sql = "UPDATE `shop_commodity` SET `com_sizes` = $sizes WHERE `commodity_ID` = $id";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            return self::MSG_SUCC_SIZE;
        } else {
            $this->dbh = new \PDO(self::DSN, self::USER, self::PASSWORD);
            echo $this->dbh->exec($sql);
            return self::MSG_NOT_SUCC_SIZE;
        }
    }

    /***
     * Sets price into makewear DB.
     * @param $id Int - com. id.
     * @param $price String - com. price from cardo site.
     * @return bool
     */
    public function updateOptPrice($id, $price)
    {
        $price = $this->dbh->quote($price);
        $sql = "UPDATE `shop_commodity` SET `commodity_price2` = $price WHERE `commodity_ID` = $id";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function addQuantity ($id, $size, $quantity)
    {
        $size = $this->dbh->quote($size);
        $sql = "INSERT INTO `shop_cardo_sizes`(`commodity_id`, `size`, `quantity`)
VALUES ($id, $size, $quantity)";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            return self::MSG_SUCC_QUANT;
        } else {
            $this->dbh = new \PDO(self::DSN, self::USER, self::PASSWORD);
            echo $this->dbh->exec($sql);
            return self::MSG_NOT_SUCC_QUANT;
        }
    }

    public function updateQuantity ($id, $size, $quantity) {
        $size = $this->dbh->quote($size);
        $sql = "UPDATE `shop_cardo_sizes` SET `quantity`=$quantity WHERE `commodity_id` = $id  AND `size` = $size";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            return self::MSG_SUCC_QUANT_UP;
        } else {
            $this->dbh = new \PDO(self::DSN, self::USER, self::PASSWORD);
            echo $this->dbh->exec($sql);
            return self::MSG_NOT_SUCC_QUANT_UP;
        }
    }

    public function getAllQuantity () {
        $sql = "SELECT 	commodity_id, size, quantity
FROM  `shop_cardo_sizes` ";
        $stmt = $this->dbh->query($sql);
        $sizes =array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sizes[] = $row;
        }
        return $sizes;
    }
    /**
     * Check if exist size in database
     * @param Int $id, String $name of size
     * @return false or array
     **/
    public function checkExistSize($id, $size)
    {
        $sizes = array();
        foreach ($this->quantities as $row) {
            if ($row["commodity_id"] == $id) {
                $sizes[$row["size"]] = $row['quantity'];
            }
        }
        $duplicate = false;
        foreach ($sizes as $key => $value) {
            if ($key == $size) {
                $duplicate['check'] = true;
                $duplicate['quantity'] = $value;
            }
        }
        if ($duplicate['check']) {
            return $duplicate;
        } else {
            return false;
        }
    }

    public function checkActualSizes ($id, $sizes)
    {
        if ($this->allSizes[$id] == $sizes) {
            return true;
        } else {
            return false;
        }
    }

    /***
     * Compare price from cardo site and makewear DB.
     * @param $id Int - com. id.
     * @param $price String - com. price from cardo site.
     * @return bool
     */
    public function checkActualOptPrices ($id, $price)
    {
        if ($this->allOptPrices[$id] == $price) {
            return true;
        } else {
            return false;
        }
    }

    /***
     * @param $count
     * @param $step
     * @param $updated
     * @param $result
     */
    public function setInterface($count, $step, $updated, $result)
    {
        $a=$count/100;
        $a2=round($step/$a, 2);
        $andSql = '';
        if ($step == 1) {
            $andSql .=', `update_add`=0';
        }
        if ($updated) {
            $andSql .=', `update_add`=`update_add`+1';
        }
        $sql = "
UPDATE `parser_interface`
 SET `update_prog`='{$a2}', `check_prog`='{$step}', `text`='{$result}'{$andSql}
 WHERE `par_id`='5'";
        $this->dbh->query('SET names utf8');
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            echo "Good";
        } else {
            echo "Not Good";
        }
    }

    public function setInterfaceComplete($result)
    {
    $sql = "
UPDATE `parser_interface`
SET `text`='{$result}'
WHERE `par_id`='5'";
    $this->dbh->query('SET names utf8');
    $this->dbh->exec($sql);
    }

    public function getUpdated()
    {
        $sql = "
SELECT 	update_add
FROM  `parser_interface`
WHERE `par_id`='5'";
        $stmt = $this->dbh->query($sql);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return $row['update_add'];
        } else {
            return false;
        }
    }

    public function setAv() {
        $sthr = 'Не отображать!ID = 1416
URL = http://cardo-ua.com/platya/45--.html
Не отображать!ID = 1417
URL = http://cardo-ua.com/platya/46--.html
Не отображать!ID = 1419
URL = http://cardo-ua.com/platya/54--.html
Не отображать!ID = 1420
URL = http://cardo-ua.com/platya/55--.html
Не отображать!ID = 1421
URL = http://cardo-ua.com/platya/56--.html
Не отображать!ID = 1512
URL = http://cardo-ua.com/platya/544-plate-kerri-ashy-gray-osen-zima-2014.html
Не отображать!ID = 1516
URL = http://cardo-ua.com/platya/566-plate-luxe-classic-black.html
Не отображать!ID = 1519
URL = http://cardo-ua.com/platya/571-plate-sofia-powder-beige.html
Не отображать!ID = 1520
URL = http://cardo-ua.com/platya/572-plate-sofia-malina.html
Не отображать!ID = 1521
URL = http://cardo-ua.com/platya/574-plate-amber-coral.html
Не отображать!ID = 1522
URL = http://cardo-ua.com/platya/580-plate-luxe-deep-purple.html
Не отображать!ID = 1524
URL = http://cardo-ua.com/platya/583-plate-mona-chocolate.html
Не отображать!ID = 1526
URL = http://cardo-ua.com/platya/590-plate-kerri-dark-gray.html
Не отображать!ID = 1527
URL = http://cardo-ua.com/platya/598-plate-vanessa-print.html
Не отображать!ID = 1530
URL = http://cardo-ua.com/platya/606-plate-liga-black-osen-zima-2015.html
Не отображать!ID = 1531
URL = http://cardo-ua.com/platya/610-plate-sana-shiny-black-zima-2015.html
Не отображать!ID = 1534
URL = http://cardo-ua.com/platya/617-plate-gloss-melanj-osen-zima-2015.html
Не отображать!ID = 1536
URL = http://cardo-ua.com/platya/623-plate-diana-dark-blue-osen-zima-2015.html
Не отображать!ID = 1537
URL = http://cardo-ua.com/platya/624-plate-adagio-flowers-osen-zima-2015.html
Не отображать!ID = 1541
URL = http://cardo-ua.com/platya/635-plate-gloss-bej-osen-zima-2015.html
Не отображать!ID = 1542
URL = http://cardo-ua.com/platya/636-plate-adriana-powder-osen-zima-2015.html
Не отображать!ID = 1544
URL = http://cardo-ua.com/platya/638-plate-celine-kapuchino-osen-zima-2015.html
Не отображать!ID = 1546
URL = http://cardo-ua.com/platya/640-plate-gloss-volna-osen-zima-2015.html
Не отображать!ID = 1547
URL = http://cardo-ua.com/platya/644-plate-diana-capuccino-osen-zima-2015.html
Не отображать!ID = 1548
URL = http://cardo-ua.com/platya/648-plate-diana-coral-osen-zima-2015.html
Не отображать!ID = 1552
URL = http://cardo-ua.com/platya/655-plate-lima-melange-osen-zima-2015.html
Не отображать!ID = 1554
URL = http://cardo-ua.com/platya/657-plate-geo-black-osen-zima-2015.html
Не отображать!ID = 1558
URL = http://cardo-ua.com/platya/665-plate-fiona-powder-beige.html
Не отображать!ID = 1563
URL = http://cardo-ua.com/platya/675-plate-blanca-black-zima-2015.html
Не отображать!ID = 1564
URL = http://cardo-ua.com/platya/676-plate-blanca-mint-zima-2015.html
Не отображать!ID = 1565
URL = http://cardo-ua.com/platya/677-plate-blanca-blue-zima-2015.html
Не отображать!ID = 1568
URL = http://cardo-ua.com/platya/680-plate-flora-blackblue-zima-2015.html
Не отображать!ID = 1569
URL = http://cardo-ua.com/platya/681-plate-flora-blackmint-zima-2015.html
Не отображать!ID = 1570
URL = http://cardo-ua.com/platya/684-plate-fiona-soft-peach-osen-zima-2015.html
Не отображать!ID = 1571
URL = http://cardo-ua.com/platya/685-plate-fiona-fresh-mint-osen-zima-2015.html
Не отображать!ID = 1572
URL = http://cardo-ua.com/platya/686-plate-fiona-classic-black-osen-zima-2015.html
Не отображать!ID = 1573
URL = http://cardo-ua.com/platya/687-plate-folk-blue-zima-2015.html
Не отображать!ID = 1574
URL = http://cardo-ua.com/platya/688-plate-folk-mint-zima-2015.html
Не отображать!ID = 1575
URL = http://cardo-ua.com/platya/689-plate-adagio-flowersbeige-zima-2015.html
Не отображать!ID = 1576
URL = http://cardo-ua.com/platya/690-plate-adagio-flowersmint-zima-2015.html
Не отображать!ID = 1577
URL = http://cardo-ua.com/platya/691-plate-montana-black-zima-2015.html
Не отображать!ID = 1578
URL = http://cardo-ua.com/platya/692-plate-montana-beige-zima-2015.html
Не отображать!ID = 1580
URL = http://cardo-ua.com/platya/696-plate-long-morskaja-volna-zima-2015.html
Не отображать!ID = 1581
URL = http://cardo-ua.com/platya/697-plate-long-gorchitsa-zima-2015.html
Не отображать!ID = 1582
URL = http://cardo-ua.com/platya/698-plate-ursula-flowers-zima-2015.html
Не отображать!ID = 1583
URL = http://cardo-ua.com/platya/699-komplekt-sigma-beige-zima-2015.html
Не отображать!ID = 1586
URL = http://cardo-ua.com/platya/703-plate-molly-black-zima-2015.html
Не отображать!ID = 1589
URL = http://cardo-ua.com/platya/708-komplekt-sigma-dark-blue-zima-2015.html
Не отображать!ID = 1590
URL = http://cardo-ua.com/platya/709-komplekt-sigma-mint-grean-zima-2015.html
Не отображать!ID = 1592
URL = http://cardo-ua.com/platya/711-plate-mila-mint-zima-2015.html
Не отображать!ID = 1594
URL = http://cardo-ua.com/platya/714-plate-slim-flowers-vesna-2015.html
Не отображать!ID = 1595
URL = http://cardo-ua.com/platya/715-plate-silvia-black-vesna-2015.html
Не отображать!ID = 1596
URL = http://cardo-ua.com/platya/718-plate-silvia-mint-vesna-2015.html
Не отображать!ID = 1597
URL = http://cardo-ua.com/platya/719-plate-silvia-beige-vesna-2015.html
Не отображать!ID = 1598
URL = http://cardo-ua.com/platya/720-plate-long-pesok-zima-2015.html
Не отображать!ID = 1600
URL = http://cardo-ua.com/platya/725-plate-lika-kletka-red-zima-2015.html
Не отображать!ID = 1601
URL = http://cardo-ua.com/platya/726-plate-kim-malina-vesna-2015.html
Не отображать!ID = 1602
URL = http://cardo-ua.com/platya/727-plate-kim-light-beige-vesna-2015.html
Не отображать!ID = 1603
URL = http://cardo-ua.com/platya/728-plate-kim-bright-blue-vesna-2015.html
Не отображать!ID = 1604
URL = http://cardo-ua.com/platya/729-plate-gia-soft-peach-vesna-2015.html
Не отображать!ID = 1606
URL = http://cardo-ua.com/platya/731-komplekt-regina-peach-vesna-2015.html
Не отображать!ID = 1607
URL = http://cardo-ua.com/platya/732-komplekt-regina-cappuccino-vesna-2015.html
Не отображать!ID = 1608
URL = http://cardo-ua.com/platya/733-plate-fler-kletka-red-vesna-2015.html
Не отображать!ID = 1609
URL = http://cardo-ua.com/platya/734-plate-tiamo-v-goroshek-vesna-2015.html
Не отображать!ID = 1610
URL = http://cardo-ua.com/platya/735-plate-sandy-beige-vesna-2015.html
Не отображать!ID = 1611
URL = http://cardo-ua.com/platya/736-plate-sandy-mint-vesna-2015.html
Не отображать!ID = 1612
URL = http://cardo-ua.com/platya/737-plate-sandy-black-vesna-2015.html
Не отображать!ID = 1613
URL = http://cardo-ua.com/platya/738-plate-sandi-rose.html
Не отображать!ID = 1614
URL = http://cardo-ua.com/platya/739-plate-favola-kletka-red-vesna2015.html
Не отображать!ID = 1615
URL = http://cardo-ua.com/platya/740-plate-dolly-malahit-vesna-2015.html
Не отображать!ID = 1616
URL = http://cardo-ua.com/platya/741-plate-dolly-fiolet-vesna-2015.html
Не отображать!ID = 1617
URL = http://cardo-ua.com/platya/742-plate-sandi-bordo-vesna2015.html
Не отображать!ID = 1618
URL = http://cardo-ua.com/platya/743-plate-sandi-sochnyy-persik-vesna2015.html
Не отображать!ID = 1619
URL = http://cardo-ua.com/platya/744-plate-gia-violet-vesna-2015.html
Не отображать!ID = 1620
URL = http://cardo-ua.com/platya/745-plate-gia-mint-green-vesna-2015.html
Не отображать!ID = 1621
URL = http://cardo-ua.com/platya/746-komplekt-regina-black-vesna-2015.html
Не отображать!ID = 1622
URL = http://cardo-ua.com/platya/747-plate-gia-red-vesna-2015.html
Не отображать!ID = 1625
URL = http://cardo-ua.com/platya/756-komplekt-roxy-dark-blue-vesna-2015.html
Не отображать!ID = 1626
URL = http://cardo-ua.com/platya/757-plate-fema-piton-mentol-vesna-2015.html
Не отображать!ID = 1627
URL = http://cardo-ua.com/platya/758-plate-nelli-black-vesna-2015.html
Не отображать!ID = 1628
URL = http://cardo-ua.com/platya/759-plate-space-melange-vesna-2015.html
Не отображать!ID = 1629
URL = http://cardo-ua.com/platya/760-plate-adel-v-goroshek-vesna-2015.html
Не отображать!ID = 1630
URL = http://cardo-ua.com/platya/763-plate-space-mint-vesna-2015.html
Не отображать!ID = 1631
URL = http://cardo-ua.com/platya/764-plate-space-dark-blue-vesna-2015.html
Не отображать!ID = 1632
URL = http://cardo-ua.com/platya/765-komplekt-roxy-mint-vesna-2015.html
Не отображать!ID = 1633
URL = http://cardo-ua.com/platya/766-komplekt-roxy-beige-vesna-2015.html
Не отображать!ID = 1651
URL = http://cardo-ua.com/trikotaj/516-kofta-jilian-korall-gipur.html
Не отображать!ID = 1660
URL = http://cardo-ua.com/trikotaj/553-kofta-granda-violet.html
Не отображать!ID = 1661
URL = http://cardo-ua.com/trikotaj/561-kofta-jenna-classic-black-osen-zima-2014.html
Не отображать!ID = 1662
URL = http://cardo-ua.com/trikotaj/562-kofta-jenna-classic-black-osen-zima-2014.html
Не отображать!ID = 1664
URL = http://cardo-ua.com/trikotaj/570-kofta-merida-black-white.html
Не отображать!ID = 1665
URL = http://cardo-ua.com/trikotaj/573-kofta-golden-zipp-beige.html
Не отображать!ID = 1666
URL = http://cardo-ua.com/trikotaj/575-kofta-jenna-deep-blue.html
Не отображать!ID = 1667
URL = http://cardo-ua.com/trikotaj/576-kofta-golden-zipp-chocolate.html
Не отображать!ID = 1671
URL = http://cardo-ua.com/trikotaj/585-kofta-merida-black-coral.html
Не отображать!ID = 1674
URL = http://cardo-ua.com/trikotaj/589-kofta-jenna-gray.html
Не отображать!ID = 1683
URL = http://cardo-ua.com/trikotaj/716-kofta-tunika-rendom-beige-vesna-2015.html
Не отображать!ID = 1684
URL = http://cardo-ua.com/trikotaj/721-kofta-tunika-rendom-melange-vesna-2015.html
Не отображать!ID = 1685
URL = http://cardo-ua.com/trikotaj/722-kofta-tunika-rendom-mint-vesna-2015.html
Не отображать!ID = 1687
URL = http://cardo-ua.com/trikotaj/749-kofta-basic-zelenyy-buket-vesna-2015.html
Не отображать!ID = 1688
URL = http://cardo-ua.com/pidjaki/419-pidjak-denfi-bejevyy-osen-2014.html
Не отображать!ID = 1691
URL = http://cardo-ua.com/pidjaki/432-pidjak-helen-osen-2014.html
Не отображать!ID = 1700
URL = http://cardo-ua.com/zenskaya-odezda/315-mayka-holli-malina-leto-2014.html
Не отображать!ID = 1702
URL = http://cardo-ua.com/zenskaya-odezda/343-futbolka-epic-leto-2014.html
Не отображать!ID = 1706
URL = http://cardo-ua.com/zenskaya-odezda/391-top-bona-.html
Не отображать!ID = 1707
URL = http://cardo-ua.com/zenskaya-odezda/394-top-bona-3d-leto-2014.html
Не отображать!ID = 2118
URL = http://cardo-ua.com/ubki/426-shorty-art-print-tsvety-osen-2014.html
Не отображать!ID = 2119
URL = http://cardo-ua.com/ubki/752-ubka-pils-zipp-vesna2015.html?????,??????
Не отображать!ID = 2131
URL = http://cardo-ua.com/platya/603-plate-ani-beige-skin-osen-zima-2015.html
Не отображать!ID = 2132
URL = http://cardo-ua.com/platya/605-plate-sana-chocolate-zima-2015.html
Не отображать!ID = 2133
URL = http://cardo-ua.com/platya/614-plate-jordan-sandy-beige-osen-zima-2015.html
Не отображать!ID = 2736
URL = http://cardo-ua.com/kombinezoni/962-kombinezon-axel-dark-blue-vesna-leto-2015.html
Не отображать!ID = 2923
URL = http://cardo-ua.com/platya/954-plate-riko-jeans-vesna-leto-2015.html
Не отображать!ID = 2924
URL = http://cardo-ua.com/platya/955-plate-flip-djins-vesna-leto-2015.html
Не отображать!ID = 2925
URL = http://cardo-ua.com/platya/961-plate-dakota-dark-blue-vesna-leto-2015.html
Не отображать!ID = 2926
URL = http://cardo-ua.com/963-bouki-otis-dark-blue-vesna-leto-2015.html
Не отображать!ID = 2928
URL = http://cardo-ua.com/platya/965-plate-adeo-lazurnyy-gobelen-vesna-leto-2015.html
Не отображать!ID = 2929
URL = http://cardo-ua.com/964-bruki-banany-stin-blackbeige-vesna-leto-2015.html
Не отображать!ID = 2930
URL = http://cardo-ua.com/platya/949-plate-klim-kletka-orange-vesna-leto-2015.html
Не отображать!ID = 2931
URL = http://cardo-ua.com/platya/948-plate-adis-gray-melange-vesna-leto-2015.html
Не отображать!ID = 2932
URL = http://cardo-ua.com/platya/947-plate-delfi-berry-vesna-leto-2015.html
Не отображать!ID = 2933
URL = http://cardo-ua.com/platya/936-plate-telli-fioletovyy-buket-vesna-leto-2015.html
Не отображать!ID = 2934
URL = http://cardo-ua.com/kombinezoni/934-kombinezon-gillian-black-vesna-leto-2015.html
Не отображать!ID = 2935
URL = http://cardo-ua.com/trikotaj/933-kofta-tema-melange-vesna-leto-2015.html
Не отображать!ID = 2936
URL = http://cardo-ua.com/928-bluza-hilly-white-vesna-leto2015.html
Не отображать!ID = 2937
URL = http://cardo-ua.com/927-plate-telli-zelenyy-buket-vesna-leto-2015.html
Не отображать!ID = 2938
URL = http://cardo-ua.com/zenskaya-odezda/899-kurtka-bomber-real-cellblack-vesna-2015.html
Не отображать!ID = 3588
URL = http://cardo-ua.com/platya/943-plate-milan-flowers-on-white-vesna-leto-2015.html
Не отображать!ID = 3589
URL = http://cardo-ua.com/kombinezoni/941-kombinezon-interi-jeans-vesna-2015.html
Не отображать!ID = 3590
URL = http://cardo-ua.com/bluzi/940-rubashka-mocca-black-vesna-leto2015.html
Не отображать!ID = 3591
URL = http://cardo-ua.com/platya/939-plate-klim-kletka-black-vesna-leto-2015.html
Не отображать!ID = 3592
URL = http://cardo-ua.com/platya/938-plate-adis-gray-jeans-vesna-leto-2015.html
Не отображать!ID = 3593
URL = http://cardo-ua.com/platya/937-plate-spring-tsvety-na-belom-vesna-leto-2015.html
Не отображать!ID = 3594
URL = http://cardo-ua.com/trikotaj/932-top-jetta-gray-koja-vesna-leto-2015.html
Не отображать!ID = 3595
URL = http://cardo-ua.com/bluzi/929-bluza-hilly-dark-blue-vesna-leto2015.html
Не отображать!ID = 3596
URL = http://cardo-ua.com/926-plate-milan-lila-colibri-vesna-leto-2015.html
Не отображать!ID = 3597
URL = http://cardo-ua.com/925-plate-riko-melange-vesna-leto-2015.html
Не отображать!ID = 3598
URL = http://cardo-ua.com/platya/924-plate-oust-melanj-vesna-leto-2015.html
Не отображать!ID = 3599
URL = http://cardo-ua.com/platya/922-plate-terra-black-vesna-leto-2015.html
Не отображать!ID = 3600
URL = http://cardo-ua.com/kombinezoni/921-kombinezon-interi-melange-vesna-2015.html
Не отображать!ID = 3601
URL = http://cardo-ua.com/bluzi/920-bluza-top-rossi-blue-vesna-leto2015.html
Не отображать!ID = 3603
URL = http://cardo-ua.com/platya/918-plate-neri-rozy-vesna-leto-2015.html
Не отображать!ID = 3604
URL = http://cardo-ua.com/917-plate-neri-tsvety-vesna-leto-2015.html
Не отображать!ID = 3605
URL = http://cardo-ua.com/916-plate-daria-tsvety-vesna-leto-2015.html
Не отображать!ID = 3607
URL = http://cardo-ua.com/915-plate-terra-capuccino-vesna-leto-2015.html
Не отображать!ID = 3608
URL = http://cardo-ua.com/bluzi/914-bluza-kimo-milky-flower-vesna-leto2015-.html
Не отображать!ID = 3609
URL = http://cardo-ua.com/bluzi/913-rubashka-nensi-mint-vesna-2015.html
Не отображать!ID = 3610
URL = http://cardo-ua.com/912-plate-oust-seroe-vesna-leto-2015.html
Не отображать!ID = 3611
URL = http://cardo-ua.com/911-kostum-anastasia-black-skin-vesna-leto-2015.html
Не отображать!ID = 3612
URL = http://cardo-ua.com/platya/910-kostum-anastasia-beige-skin-vesna-leto-2015.html
Не отображать!ID = 3613
URL = http://cardo-ua.com/platya/909-plate-nixi-bordo-vesna-leto-2015.html
Не отображать!ID = 3614
URL = http://cardo-ua.com/platya/908-plate-nixi-black-vesna-leto-2015.html
Не отображать!ID = 3615
URL = http://cardo-ua.com/907-plate-nixi-gusinaja-lapka-vesna-leto-2015.html
Не отображать!ID = 3616
URL = http://cardo-ua.com/906-plate-darsi-red-vesna-leto-2015.html
Не отображать!ID = 3617
URL = http://cardo-ua.com/platya/905-plate-vio-mentol-vesna-leto-2015.html
Не отображать!ID = 3618
URL = http://cardo-ua.com/904-plate-vio-jeltyy-vesna-leto-2015.html
Не отображать!ID = 3619
URL = http://cardo-ua.com/platya/902-plate-vio-persik-vesna-leto-2015.html
Не отображать!ID = 3620
URL = http://cardo-ua.com/900-plate-sweet-romashki-vesna-leto-2015.html
Не отображать!ID = 3622
URL = http://cardo-ua.com/zenskaya-odezda/897-kofta-sammy-dark-blue-vesna-2015.html
Не отображать!ID = 3623
URL = http://cardo-ua.com/896-kofta-sammy-black-vesna-2015.html
Не отображать!ID = 3624
URL = http://cardo-ua.com/platya/895-plate-sfera-beige-vesna-leto-2015.html
Не отображать!ID = 3626
URL = http://cardo-ua.com/bluzi/977-rubashka-ice-kletka-black-vesna-leto2015.html
Не отображать!ID = 3627
URL = http://cardo-ua.com/976-rubashka-ice-kletka-blue-vesna-leto2015.html
Не отображать!ID = 3628
URL = http://cardo-ua.com/platya/975-plate-sketch-melange-vesna-leto-2015.html
Не отображать!ID = 3630
URL = http://cardo-ua.com/platya/973-plate-rubashka-eco-kletka-blue-vesna-leto-2015.html
Не отображать!ID = 3631
URL = http://cardo-ua.com/platya/972-plate-tina-magnolija-seryy-vesna-leto-2015.html
Не отображать!ID = 3633
URL = http://cardo-ua.com/970-bluza-kimo-dark-blue-flower-vesna-leto2015.html
Не отображать!ID = 3634
URL = http://cardo-ua.com/platya/969-plate-klim-kletka-blue-vesna-leto-2015.html
Не отображать!ID = 3635
URL = http://cardo-ua.com/platya/968-plate-adeo-romashki-vesna-leto-2015.html
Не отображать!ID = 3636
URL = http://cardo-ua.com/platya/967-plate-adeo-oranjevye-babochki-vesna-leto-2015.html
Не отображать!ID = 3637
URL = http://cardo-ua.com/platya/960-plate-cup-kletka-orange-vesna-leto-2015.html
Не отображать!ID = 3639
URL = http://cardo-ua.com/kombinezoni/959-kombinezon-jeneva-goroh-vesna-2015.html
Не отображать!ID = 3640
URL = http://cardo-ua.com/platya/958-plate-spring-kolibri-vesna-leto-2015.html
Не отображать!ID = 3641
URL = http://cardo-ua.com/platya/957-plate-rubashka-teo-black-vesna-leto-2015.html
Не отображать!ID = 3642
URL = http://cardo-ua.com/platya/956-plate-flip-seryy-vesna-leto-2015.html
Не отображать!ID = 3643
URL = http://cardo-ua.com/platya/953-plate-betta-light-blue-vesna-leto-2015.html
Не отображать!ID = 3644
URL = http://cardo-ua.com/platya/952-plate-betta-capuccino-vesna-leto-2015.html
Не отображать!ID = 3645
URL = http://cardo-ua.com/platya/951-plate-betta-bordo-vesna-leto-2015.html
Не отображать!ID = 3647
URL = http://cardo-ua.com/platya/946-plate-delfi-lila-vesna-leto-2015.html
Не отображать!ID = 3648
URL = http://cardo-ua.com/platya/945-plate-delfi-peach-vesna-leto-2015.html
Не отображать!ID = 5933
URL = http://cardo-ua.com/zenskaya-odezda/978-kofta-destiny-gray-koja-vesna-leto-2015.html
Не отображать!ID = 5934
URL = http://cardo-ua.com/platya/983-plate-magnolia-mint-vesna-leto-2015.html
Не отображать!ID = 5935
URL = http://cardo-ua.com/platya/982-plate-magnolia-light-gray-vesna-leto-2015.html
Не отображать!ID = 5936
URL = http://cardo-ua.com/platya/981-plate-magnolia-milk-vesna-leto-2015.html
Не отображать!ID = 5937
URL = http://cardo-ua.com/platya/980-plate-tina-magnolija-moloko-vesna-leto-2015.html
Не отображать!ID = 5938
URL = http://cardo-ua.com/platya/944-plate-delfi-mint-vesna-leto-2015.html
Не отображать!ID = 5939
URL = http://cardo-ua.com/platya/942-plate-milan-butterfly-vesna-leto-2015.html
Не отображать!ID = 7326
URL = http://cardo-ua.com/platya/985-plate-tina-magnolija-mentol-leto-2015.html
Не отображать!ID = 7327
URL = http://cardo-ua.com/platya/988-plate-dakota-gray-vesna-leto-2015.html
Не отображать!ID = 7328
URL = http://cardo-ua.com/platya/987-plate-cup-kletka-black-vesna-leto-2015.html
Не отображать!ID = 7329
URL = http://cardo-ua.com/platya/986-plate-cup-kletka-blue-vesna-leto-2015.html
Не отображать!ID = 7330
URL = http://cardo-ua.com/ubki/989-ubka-westly-cherry-vesna-leto2015.html
Не отображать!ID = 7331
URL = http://cardo-ua.com/ubki/990-ubka-westly-camomile-vesna-leto2015.html
Не отображать!ID = 7332
URL = http://cardo-ua.com/platya/991-plate-pestodark-blueblack-vesna-leto-2015.html
Не отображать!ID = 7333
URL = http://cardo-ua.com/ubki/995-ubka-grant-light-gray-vesna-leto2015.html
Не отображать!ID = 7334
URL = http://cardo-ua.com/ubki/994-ubka-optima-beige-vesna-leto2015.html
Не отображать!ID = 7335
URL = http://cardo-ua.com/ubki/992-ubka-foll-beige-vesna-leto2015.html
Не отображать!ID = 7336
URL = http://cardo-ua.com/platya/996-plate-ella-gobelen-vesna-leto-2015.html
Не отображать!ID = 7337
URL = http://cardo-ua.com/platya/997-plate-ivora-white-vesna-leto-2015.html
Не отображать!ID = 7338
URL = http://cardo-ua.com/platya/998-plate-marsi-dark-blue-vesna-leto-2015.html
Не отображать!ID = 7339
URL = http://cardo-ua.com/platya/1001-plate-gvinet-fistashka-vesna-leto-2015.html
Не отображать!ID = 7340
URL = http://cardo-ua.com/platya/1000-plate-gvinet-black-vesna-leto-2015.html
Не отображать!ID = 7342
URL = http://cardo-ua.com/platya/1002-plate-gvinet-light-blue-vesna-leto-2015.html
Не отображать!ID = 7343
URL = http://cardo-ua.com/bluzi/1003-top-s-baskoy-defin-kletka-black-vesna-leto2015.html
Не отображать!ID = 7344
URL = http://cardo-ua.com/platya/1004-letnee-plate-margi-dark-blue-vesna-leto-2015.html
Не отображать!ID = 7374
URL = http://cardo-ua.com/platya/1007-plate-rubashka-eco-kletka-black-leto-2015.html
Не отображать!ID = 7375
URL = http://cardo-ua.com/kombinezoni/1006-kombinezon-artego-romashki-leto-2015.html
Не отображать!ID = 7376
URL = http://cardo-ua.com/kombinezoni/1005-kombinezon-artego-gray-colibri-leto-2015.html
Не отображать!ID = 7377
URL = http://cardo-ua.com/platya/1008-plate-ella-babochki-leto-2015.html
Не отображать!ID = 7378
URL = http://cardo-ua.com/platya/1009-plate-ella-temno-sinee-leto-2015.html
Не отображать!ID = 7379
URL = http://cardo-ua.com/platya/1010-plate-dakota-bordo-leto-2015.html
Не отображать!ID = 10831
URL = http://cardo-ua.com/zenskaya-odezda/1262-mayka-perseya-melkiy-buket-na-temno-sinem-leto-2015.html
Не отображать!ID = 10832
URL = http://cardo-ua.com/zenskaya-odezda/1261-futbolka-perseya-temno-sinjaja-v-buton-leto-2015.html
Не отображать!ID = 10833
URL = http://cardo-ua.com/zenskaya-odezda/1260-mayka-perseya-moloko-tsvety-leto-2015.html
Не отображать!ID = 10834
URL = http://cardo-ua.com/zenskaya-odezda/1259-mayka-perseya-golubye-tsvety-na-belom-leto-2015.html
Не отображать!ID = 10835
URL = http://cardo-ua.com/platya/1258-plate-amur-temno-seroe-v-melkiy-tsvetok-leto-2015.html
Не отображать!ID = 10836
URL = http://cardo-ua.com/platya/1257-plate-amor-chernoe-v-melkiy-belyy-goroshek-leto-2015.html
Не отображать!ID = 10837
URL = http://cardo-ua.com/platya/1256-plate-jell-tsvety-na-belom-temno-goluboy-djins-leto-2015.html
Не отображать!ID = 10839
URL = http://cardo-ua.com/platya/1254-plate-amur-beloe-s-venzeljami-leto-2015.html
Не отображать!ID = 10841
URL = http://cardo-ua.com/platya/1252-plate-seam-temno-siniy-jeans-vesna-leto-2015.html
Не отображать!ID = 10842
URL = http://cardo-ua.com/platya/1251-plate-vindi-beloe-v-tsvetochek-leto-2015.html
Не отображать!ID = 10843
URL = http://cardo-ua.com/platya/1250-plate-vindi-temno-sinee-v-tsvetochek-leto-2015.html
Не отображать!ID = 10844
URL = http://cardo-ua.com/platya/1249-plate-vindi-gorchitsa-leto-2015.html
Не отображать!ID = 10845
URL = http://cardo-ua.com/platya/1248-plate-rose-chernoe-leto-2015.html
Не отображать!ID = 10847
URL = http://cardo-ua.com/platya/1245-plate-gess-svetlyy-persik-leto-2015.html
Не отображать!ID = 10848
URL = http://cardo-ua.com/platya/1244-plate-meredit-oazis-leto-2015.html
Не отображать!ID = 10849
URL = http://cardo-ua.com/platya/1243-plate-next-kletka-bordo-leto-2015.html
Не отображать!ID = 10850
URL = http://cardo-ua.com/platya/1242-plate-blase-kletka-bordo-leto-2015.html
Не отображать!ID = 10851
URL = http://cardo-ua.com/platya/1241-sarafan-marin-goluboy-leto-2015.html
Не отображать!ID = 10852
URL = http://cardo-ua.com/platya/1240-plate-marin-korall-leto-2015.html
Не отображать!ID = 10853
URL = http://cardo-ua.com/platya/1239-plate-marin-zelenaja-biruza-leto-2015.html
Не отображать!ID = 10854
URL = http://cardo-ua.com/platya/1238-plate-camellia-seroe-kolibri-leto-2015.html
Не отображать!ID = 10855
URL = http://cardo-ua.com/platya/1237-plate-veneta-svetlo-seroe-kolibri-leto-2015.html
Не отображать!ID = 10856
URL = http://cardo-ua.com/zenskaya-odezda/1236-futbolka-sicilia-mentol-v-goroshek-leto-2015.html
Не отображать!ID = 10857
URL = http://cardo-ua.com/zenskaya-odezda/1235-futbolka-sicilia-persik-v-goroshek-leto-2015.html
Не отображать!ID = 10858
URL = http://cardo-ua.com/zenskaya-odezda/1234-futbolka-sicilia-chernaja-v-goroshek-leto-2015.html
Не отображать!ID = 10859
URL = http://cardo-ua.com/zenskaya-odezda/1233-futbolka-sicilia-belaja-v-goroshek-leto-2015.html
Не отображать!ID = 10860
URL = http://cardo-ua.com/platya/1231-plate-jasmin-oranjevye-babochki-leto-2015.html
Не отображать!ID = 10861
URL = http://cardo-ua.com/platya/1230-plate-amor-beloe-v-melkiy-chernyy-goroshek-leto-2015.html
Не отображать!ID = 10862
URL = http://cardo-ua.com/platya/1229-plate-miomi-temno-sinee-leto-2015.html
Не отображать!ID = 10863
URL = http://cardo-ua.com/zenskaya-odezda/1228-futbolka-chic-moloko-buket-leto-2015.html
Не отображать!ID = 10864
URL = http://cardo-ua.com/zenskaya-odezda/1227-futbolka-chic-temno-sinjaja-buton-leto-2015.html
Не отображать!ID = 10865
URL = http://cardo-ua.com/zenskaya-odezda/1226-futbolka-kilian-temno-sinjaja-s-uzorom-leto-2015.html
Не отображать!ID = 10866
URL = http://cardo-ua.com/zenskaya-odezda/1225-futbolka-kilian-belaja-s-uzorom-leto-2015.html
Не отображать!ID = 10867
URL = http://cardo-ua.com/platya/1224-plate-megan-golubye-tsvety-leto-2015.html
Не отображать!ID = 10868
URL = http://cardo-ua.com/platya/1223-sarafan-sunny-ornament-leto-2015.html
Не отображать!ID = 10869
URL = http://cardo-ua.com/platya/1222-plate-zara-chernoe-v-polosku-leto-2015.html
Не отображать!ID = 10870
URL = http://cardo-ua.com/platya/1221-sarafan-ultra-rozovyy-leto-2015.html
Не отображать!ID = 10871
URL = http://cardo-ua.com/platya/1220-sarafan-ultra-bejevoe-leto-2015.html
Не отображать!ID = 10872
URL = http://cardo-ua.com/platya/1219-plate-vido-listja-gorchitsa-leto-2015.html
Не отображать!ID = 10873
URL = http://cardo-ua.com/platya/1218-plate-vido-temno-sinee-v-romashku-leto-2015.html
Не отображать!ID = 10874
URL = http://cardo-ua.com/platya/1217-plate-vido-salatnye-babochki-leto-2015.html
Не отображать!ID = 10875
URL = http://cardo-ua.com/platya/1216-plate-camellia-beloe-v-tsvetok-leto-2015.html
Не отображать!ID = 10876
URL = http://cardo-ua.com/platya/1215-plate-camellia-temno-sinee-v-romashku-leto-2015.html
Не отображать!ID = 10877
URL = http://cardo-ua.com/platya/1214-plate-lotus-gobelen-leto-2015.html
Не отображать!ID = 10878
URL = http://cardo-ua.com/platya/1213-plate-lotus-listja-gorchitsa-leto-2015.html
Не отображать!ID = 10879
URL = http://cardo-ua.com/zenskaya-odezda/1212-futbolka-indigo-melkie-tsvetochki-na-serom-leto-2015.html
Не отображать!ID = 10880
URL = http://cardo-ua.com/platya/1210-sarafan-juliette-lilovye-uzory-leto-2015.html
Не отображать!ID = 10881
URL = http://cardo-ua.com/platya/1209-sarafan-juliette-belyy-v-siniy-tsvetok-leto-2015.html
Не отображать!ID = 10882
URL = http://cardo-ua.com/platya/1208-sarafan-sunset-siniy-v-melkuu-romashku-leto-2015.html
Не отображать!ID = 10883
URL = http://cardo-ua.com/platya/1207-sarafan-sunset-krasnyy-v-melkuu-romashku-leto-2015.html
Не отображать!ID = 10884
URL = http://cardo-ua.com/platya/1206-plate-sanaya-krasnoe-v-romashku-leto-2015.html
Не отображать!ID = 10885
URL = http://cardo-ua.com/zenskaya-odezda/1205-futbolka-kilian-tsvety-leto-2015.html
Не отображать!ID = 10886
URL = http://cardo-ua.com/zenskaya-odezda/1204-mayka-ginger-belaja-leto-2015.html
Не отображать!ID = 10887
URL = http://cardo-ua.com/zenskaya-odezda/1203-mayka-ginger-svetlo-golubaja-leto-2015.html
Не отображать!ID = 10888
URL = http://cardo-ua.com/zenskaya-odezda/1202-futbolka-indigo-molochnye-tsvety-leto-2015.html
Не отображать!ID = 10889
URL = http://cardo-ua.com/platya/1201-sarafan-sunset-chernyy-v-melkuu-romashku-leto-2015.html
Не отображать!ID = 10890
URL = http://cardo-ua.com/platya/1200-plate-veneta-serye-tsvety-leto-2015.html
Не отображать!ID = 10891
URL = http://cardo-ua.com/platya/1198-plate-juliette-sinee-v-melkuu-romashku-leto-2015.html
Не отображать!ID = 10892
URL = http://cardo-ua.com/platya/1197-plate-sanya-sinee-v-romashku-leto-2015.html
Не отображать!ID = 10893
URL = http://cardo-ua.com/platya/1196-plate-gosh-persik-v-goroshek-leto-2015.html
Не отображать!ID = 10894
URL = http://cardo-ua.com/platya/1195-plate-gosh-chernoe-v-goroshek-leto-2015.html
Не отображать!ID = 10895
URL = http://cardo-ua.com/platya/1193-sarafan-omnia-melkiy-buket-leto-2015.html
Не отображать!ID = 10896
URL = http://cardo-ua.com/platya/1192-plate-omnia-nebesnyy-sad-leto-2015.html
Не отображать!ID = 10897
URL = http://cardo-ua.com/platya/1191-plate-kenzo-temno-sinee-leto-2015.html
Не отображать!ID = 10898
URL = http://cardo-ua.com/ubki/1190-shorty-eps-mentol-leto2015.html
Не отображать!ID = 10899
URL = http://cardo-ua.com/ubki/1189-shorty-eps-belyy-leto2015.html
Не отображать!ID = 10900
URL = http://cardo-ua.com/ubki/1188-shorty-eps-svetlo-jeltyy-leto2015.html
Не отображать!ID = 10901
URL = http://cardo-ua.com/ubki/1187-shorty-eps-bej-leto2015.html
Не отображать!ID = 10902
URL = http://cardo-ua.com/ubki/1186-shorty-eps-persik-leto2015.html
Не отображать!ID = 10903
URL = http://cardo-ua.com/zenskaya-odezda/1185-futbolka-bond-temno-seraja-v-melkiy-tsvetochek-leto-2015.html
Не отображать!ID = 10904
URL = http://cardo-ua.com/zenskaya-odezda/1184-futbolka-bond-siniy-elektrik-v-belyy-goroshek-leto-2015.html
Не отображать!ID = 10905
URL = http://cardo-ua.com/zenskaya-odezda/1183-futbolka-bond-belaja-v-chernyy-goroshek-leto-2015.html
Не отображать!ID = 10906
URL = http://cardo-ua.com/zenskaya-odezda/1182-futbolka-bond-chernaja-v-belyy-goroshek-leto-2015.html
Не отображать!ID = 10907
URL = http://cardo-ua.com/zenskaya-odezda/1181-futbolka-indigo-temno-sinjaja-v-tsvetochek-leto-2015.html
Не отображать!ID = 10908
URL = http://cardo-ua.com/zenskaya-odezda/1180-top-daisy-zelenyy-leto-2015.html
Не отображать!ID = 10909
URL = http://cardo-ua.com/zenskaya-odezda/1179-futbolka-daisy-korall-leto-2015.html
Не отображать!ID = 10910
URL = http://cardo-ua.com/platya/1177-plate-bloom-chernoe-leto-2015.html
Не отображать!ID = 10911
URL = http://cardo-ua.com/platya/1176-plate-jasmin-chernoe-s-vishenkami-leto-2015.html
Не отображать!ID = 10912
URL = http://cardo-ua.com/platya/1175-plate-jasmin-beloe-s-vishenkami-leto-2015.html
Не отображать!ID = 10913
URL = http://cardo-ua.com/platya/1174-plate-venturi-goluboe-leto-2015.html
Не отображать!ID = 10915
URL = http://cardo-ua.com/platya/1172-plate-betani-svetlo-seroe-leto-2015.html
Не отображать!ID = 10916
URL = http://cardo-ua.com/platya/1171-plate-opium-temno-sinee-leto-2015.html
Не отображать!ID = 10917
URL = http://cardo-ua.com/platya/1170-plate-blanik-beloe-tsvety-leto-2015.html
Не отображать!ID = 10918
URL = http://cardo-ua.com/platya/1169-plate-fantasy-tsvety-leto-2015.html
Не отображать!ID = 10919
URL = http://cardo-ua.com/platya/1168-plate-verso-rozovoe-leto-2015.html
Не отображать!ID = 10921
URL = http://cardo-ua.com/platya/1166-plate-intense-bej-lugovye-tsvety-leto-2015.html
Не отображать!ID = 10922
URL = http://cardo-ua.com/platya/1165-plate-allegoria-gorchitsa-buket-leto-2015.html
Не отображать!ID = 10923
URL = http://cardo-ua.com/platya/1164-plate-allegoria-melkiy-buket-leto-2015.html
Не отображать!ID = 10924
URL = http://cardo-ua.com/platya/1162-plate-sanya-zelenoe-v-romashku-leto-2015.html
Не отображать!ID = 10925
URL = http://cardo-ua.com/platya/1161-plate-gosh-zelenoe-v-goroshek-leto-2015.html
Не отображать!ID = 10926
URL = http://cardo-ua.com/ubki/1160-ubka-lorenzo-seraja-tsvety-leto2015.html
Не отображать!ID = 10927
URL = http://cardo-ua.com/zenskaya-odezda/1159-futbolka-ajur-belyy-leto-2015.html
Не отображать!ID = 10929
URL = http://cardo-ua.com/platya/1157-plate-lancome-sinjaja-kletka-leto-2015.html
Не отображать!ID = 10930
URL = http://cardo-ua.com/platya/1156-plate-paloma-chernyy-goroh-leto-2015.html
Не отображать!ID = 10931
URL = http://cardo-ua.com/platya/1155-plate-airin-bejevoe-leto-2015.html
Не отображать!ID = 10932
URL = http://cardo-ua.com/1154-bruki-loran-gorchitsa-leto-2015.html
Не отображать!ID = 10933
URL = http://cardo-ua.com/platya/1153-plate-absolu-beloe-leto-2015.html
Не отображать!ID = 10934
URL = http://cardo-ua.com/platya/1152-plate-omnia-hohloma-leto-2015.html
Не отображать!ID = 10935
URL = http://cardo-ua.com/platya/1151-plate-venturi-korall-leto-2015.html
Не отображать!ID = 10936
URL = http://cardo-ua.com/zenskaya-odezda/1150-top-daisy-golubaja-leto-2015.html
Не отображать!ID = 10937
URL = http://cardo-ua.com/ubki/1149-shorty-valentino-belye-leto2015.html
Не отображать!ID = 10938
URL = http://cardo-ua.com/platya/1148-plate-fantasy-babochka-leto-2015.html
Не отображать!ID = 10939
URL = http://cardo-ua.com/platya/1147-plate-fantasy-elektrik-leto-2015.html
Не отображать!ID = 10940
URL = http://cardo-ua.com/platya/1145-plate-arden-svetlo-seroe-krupnaja-roza-leto-2015.html
Не отображать!ID = 10941
URL = http://cardo-ua.com/platya/1144-plate-jasmin-lilovoe-leto-2015.html
Не отображать!ID = 10942
URL = http://cardo-ua.com/platya/1143-plate-intense-lugovye-tsvety-leto-2015.html
Не отображать!ID = 10943
URL = http://cardo-ua.com/platya/1142-plate-intense-djins-leto-2015.html
Не отображать!ID = 10944
URL = http://cardo-ua.com/platya/1141-plate-lancome-krasnaja-kletka-leto-2015.html
Не отображать!ID = 10945
URL = http://cardo-ua.com/platya/1140-plate-paloma-hohloma-leto-2015.html
Не отображать!ID = 10946
URL = http://cardo-ua.com/ubki/1139-ubka-lesta-goluboy-leto2015.html
Не отображать!ID = 10947
URL = http://cardo-ua.com/ubki/1138-ubka-lesta-moloko-leto2015.html
Не отображать!ID = 10948
URL = http://cardo-ua.com/zenskaya-odezda/1137-futbolka-ajur-mentol-leto-2015.html
Не отображать!ID = 10949
URL = http://cardo-ua.com/zenskaya-odezda/1136-futbolka-ajur-persik-leto-2015.html
Не отображать!ID = 10950
URL = http://cardo-ua.com/zenskaya-odezda/1135-futbolka-tinsa-goluboy-leto-2015.html
Не отображать!ID = 10951
URL = http://cardo-ua.com/ubki/1134-shorty-stils-golubye-leto2015.html
Не отображать!ID = 10952
URL = http://cardo-ua.com/ubki/1132-shorty-stils-jeltyy-leto2015.html
Не отображать!ID = 10953
URL = http://cardo-ua.com/kombinezoni/1131-kombinezon-lanvin-goluboy-leto-2015.html
Не отображать!ID = 10954
URL = http://cardo-ua.com/platya/1130-plate-rock-temno-goluboy-leto-2015.html
Не отображать!ID = 10955
URL = http://cardo-ua.com/platya/1129-plate-allegoria-melkie-tsvety-leto-2015.html
Не отображать!ID = 10956
URL = http://cardo-ua.com/platya/1128-plate-paloma-morskaja-volna-leto-2015.html
Не отображать!ID = 10957
URL = http://cardo-ua.com/platya/1127-plate-arden-oazis-leto-2015.html
Не отображать!ID = 10958
URL = http://cardo-ua.com/ubki/1125-shorty-stils-moloko-leto2015.html
Не отображать!ID = 10959
URL = http://cardo-ua.com/zenskaya-odezda/1124-futbolka-tinsa-moloko-leto-2015.html
Не отображать!ID = 10960
URL = http://cardo-ua.com/zenskaya-odezda/1123-futbolka-tinsa-jeltyy-leto-2015.html
Не отображать!ID = 10961
URL = http://cardo-ua.com/1122-bruki-rest-hohloma-leto-2015.html
Не отображать!ID = 10962
URL = http://cardo-ua.com/kombinezoni/1121-kombinezon-lanvin-jeltyy-leto-2015.html
Не отображать!ID = 10963
URL = http://cardo-ua.com/kombinezoni/1120-kombinezon-lanvin-moloko-leto-2015.html
Не отображать!ID = 10964
URL = http://cardo-ua.com/platya/1119-plate-cana-sinee-leto-2015.html
Не отображать!ID = 10965
URL = http://cardo-ua.com/ubki/1118-shorty-beil-temno-golubye-leto2015.html
Не отображать!ID = 10966
URL = http://cardo-ua.com/platya/1116-plate-cana-bej-leto-2015.html
Не отображать!ID = 10967
URL = http://cardo-ua.com/platya/1115-plate-cana-chernoe-romashka-leto-2015.html
Не отображать!ID = 10968
URL = http://cardo-ua.com/platya/1114-plate-christian-cherno-belaja-kletka-leto-2015.html
Не отображать!ID = 10969
URL = http://cardo-ua.com/platya/1113-plate-airin-moloko-leto-2015.html
Не отображать!ID = 10970
URL = http://cardo-ua.com/platya/1112-plate-airin-goluboe-leto-2015.html
Не отображать!ID = 10971
URL = http://cardo-ua.com/zenskaya-odezda/1111-futbolka-memory-nebesnyy-leto-2015.html
Не отображать!ID = 10972
URL = http://cardo-ua.com/1110-futbolka-memory-svetlo-jeltaja-leto-2015.html
Не отображать!ID = 10973
URL = http://cardo-ua.com/platya/1109-plate-asia-chernoe-roza-leto-2015.html
Не отображать!ID = 10974
URL = http://cardo-ua.com/platya/1108-plate-asia-oazis-leto-2015.html
Не отображать!ID = 10975
URL = http://cardo-ua.com/platya/1107-plate-veta-beloe-v-korallovyy-goroh-leto-2015.html
Не отображать!ID = 10976
URL = http://cardo-ua.com/platya/1105-plate-ives-temno-sinee-v-rombik-leto-2015.html
Не отображать!ID = 10977
URL = http://cardo-ua.com/platya/1104-plate-ives-mozaika-leto-2015.html
Не отображать!ID = 10978
URL = http://cardo-ua.com/platya/1103-plate-ives-beloe-s-uzorom-leto-2015.html
Не отображать!ID = 10979
URL = http://cardo-ua.com/platya/1102-plate-betani-seroe-leto-2015.html
Не отображать!ID = 10980
URL = http://cardo-ua.com/platya/1101-plate-opium-svetlo-goluboe-leto-2015.html
Не отображать!ID = 10981
URL = http://cardo-ua.com/platya/1100-plate-opium-korall-leto-2015.html
Не отображать!ID = 10982
URL = http://cardo-ua.com/platya/1099-plate-kenzo-svetlo-goluboe-leto-2015.html
Не отображать!ID = 10983
URL = http://cardo-ua.com/platya/1098-plate-birkin-golubaja-kletka-leto-2015.html
Не отображать!ID = 10984
URL = http://cardo-ua.com/platya/1097-plate-evia-goluboe-s-uzorom-leto-2015.html
Не отображать!ID = 10985
URL = http://cardo-ua.com/platya/1096-letnee-plate-ameli-bejevoe-orhideja-leto-2015.html
Не отображать!ID = 10986
URL = http://cardo-ua.com/platya/1095-letnee-plate-ameli-sinee-orhideja-leto-2015.html
Не отображать!ID = 10987
URL = http://cardo-ua.com/platya/1094-plate-dolce-korall-leto-2015.html
Не отображать!ID = 10988
URL = http://cardo-ua.com/platya/1093-plate-dolce-beloe-leto-2015.html
Не отображать!ID = 10989
URL = http://cardo-ua.com/platya/1092-plate-ferre-korall-leto-2015.html
Не отображать!ID = 10990
URL = http://cardo-ua.com/platya/1091-plate-ferre-beloe-leto-2015.html
Не отображать!ID = 10991
URL = http://cardo-ua.com/platya/1090-plate-zummi-moloko-leto-2015.html
Не отображать!ID = 10992
URL = http://cardo-ua.com/platya/1089-plate-zummi-goluboe-leto-2015.html
Не отображать!ID = 10993
URL = http://cardo-ua.com/platya/1088-plate-summer-melkaja-roza-leto-2015.html
Не отображать!ID = 10994
URL = http://cardo-ua.com/platya/1087-plate-summer-melkaja-romashka-leto-2015.html
Не отображать!ID = 10995
URL = http://cardo-ua.com/platya/1086-plate-peru-mint-leto-2015.html
Не отображать!ID = 10996
URL = http://cardo-ua.com/platya/1085-plate-peru-peach-leto-2015.html
Не отображать!ID = 10997
URL = http://cardo-ua.com/platya/1084-plate-grace-temno-sinee-v-goroshek-leto-2015.html
Не отображать!ID = 10998
URL = http://cardo-ua.com/platya/1083-plate-grace-belyy-v-goroshek-leto-2015.html
Не отображать!ID = 10999
URL = http://cardo-ua.com/platya/1082-plate-lola-temno-sinee-v-goroshek-leto-2015.html
Не отображать!ID = 11000
URL = http://cardo-ua.com/ubki/1081-ubka-nona-chernaja-kletka-leto2015.html
Не отображать!ID = 11001
URL = http://cardo-ua.com/1080-futbolka-sendi-melanj-leto2015.html
Не отображать!ID = 11002
URL = http://cardo-ua.com/platya/1079-plate-dolce-chernoe-leto-2015.html
Не отображать!ID = 11003
URL = http://cardo-ua.com/platya/1078-plate-ferre-chernoe-leto-2015.html
Не отображать!ID = 11004
URL = http://cardo-ua.com/platya/1077-plate-zummi-jeltoe-leto-2015.html
Не отображать!ID = 11005
URL = http://cardo-ua.com/bluzi/1076-bluza-lewis-babochki-leto2015.html
Не отображать!ID = 11006
URL = http://cardo-ua.com/bluzi/1075-bluza-lewis-temno-sinjaja-kotiki-leto2015.html
Не отображать!ID = 11007
URL = http://cardo-ua.com/ubki/1074-ubka-leila-chernaja-kletka-leto2015.html
Не отображать!ID = 11008
URL = http://cardo-ua.com/platya/1073-plate-birkin-chernaja-kletka-leto-2015.html
Не отображать!ID = 11009
URL = http://cardo-ua.com/platya/1072-plate-escada-chernoe-v-goroshek-leto-2015.html
Не отображать!ID = 11010
URL = http://cardo-ua.com/platya/1071-plate-grace-mentol-v-goroshek-leto-2015.html
Не отображать!ID = 11011
URL = http://cardo-ua.com/platya/1070-plate-krit-babochka-leto-2015.html
Не отображать!ID = 11012
URL = http://cardo-ua.com/platya/1069-plate-krit-golubye-tsvety-leto-2015.html
Не отображать!ID = 11013
URL = http://cardo-ua.com/platya/1068-plate-krit-tsvety-leto-2015.html
Не отображать!ID = 11014
URL = http://cardo-ua.com/platya/1067-plate-lola-krasnoe-v-goroshek-leto-2015.html
Не отображать!ID = 11015
URL = http://cardo-ua.com/platya/1066-plate-fest-kletka-bordo-leto-2015.html
Не отображать!ID = 11016
URL = http://cardo-ua.com/bluzi/1065-bluza-rino-v-tsvetochek-leto2015.html
Не отображать!ID = 11017
URL = http://cardo-ua.com/bluzi/1064-bluza-rino-moloko-leto2015.html
Не отображать!ID = 11018
URL = http://cardo-ua.com/ubki/1063-ubka-grant-gray-vesna-leto2015.html
Не отображать!ID = 11019
URL = http://cardo-ua.com/ubki/1062-ubka-sharlin-dark-blue-leto2015.html
Не отображать!ID = 11020
URL = http://cardo-ua.com/ubki/1061-ubka-foll-black-vesna-leto2015.html
Не отображать!ID = 11021
URL = http://cardo-ua.com/platya/1060-plate-stils-leto-2015.html
Не отображать!ID = 11022
URL = http://cardo-ua.com/platya/1059-plate-peru-black-leto-2015.html
Не отображать!ID = 11023
URL = http://cardo-ua.com/kombinezoni/1058-kombinezon-arin-rozy-leto-2015.html
Не отображать!ID = 11024
URL = http://cardo-ua.com/kombinezoni/1057-kombinezon-arin-mozaika-leto-2015.html
Не отображать!ID = 11025
URL = http://cardo-ua.com/platya/1056-sarafan-valio-maki-leto-2015.html
Не отображать!ID = 11026
URL = http://cardo-ua.com/platya/1055-plate-ellada-red-leto-2015.html
Не отображать!ID = 11027
URL = http://cardo-ua.com/platya/1054-plate-era-beige-leto-2015.html
Не отображать!ID = 11028
URL = http://cardo-ua.com/platya/1053-plate-vida-green-leto-2015.html
Не отображать!ID = 11029
URL = http://cardo-ua.com/bluzi/1052-rubashka-oliver-kletka-bordo-leto2015.html
Не отображать!ID = 11030
URL = http://cardo-ua.com/ubki/1051-ubka-sharlin-beige-leto2015.html
Не отображать!ID = 11031
URL = http://cardo-ua.com/bluzi/1050-rubashka-oliver-kletka-red-leto2015.html
Не отображать!ID = 11032
URL = http://cardo-ua.com/platya/1049-plate-jell-rozy-na-belom-leto-2015.html
Не отображать!ID = 11033
URL = http://cardo-ua.com/platya/1048-plate-jell-lugovye-tsvety-leto-2015.html
Не отображать!ID = 11034
URL = http://cardo-ua.com/platya/1047-plate-lele-temno-seroe-leto-2015.html
Не отображать!ID = 11035
URL = http://cardo-ua.com/platya/1046-plate-lele-seroe-leto-2015.html
Не отображать!ID = 11036
URL = http://cardo-ua.com/platya/1045-plate-lele-izumrud-leto-2015.html
Не отображать!ID = 11037
URL = http://cardo-ua.com/platya/1044-plate-era-green-leto-2015.html
Не отображать!ID = 11038
URL = http://cardo-ua.com/platya/1043-plate-ellada-blue-leto-2015.html
Не отображать!ID = 11039
URL = http://cardo-ua.com/platya/1042-plate-spring-fioletovyy-kolibri-vesna-leto-2015.html
Не отображать!ID = 11040
URL = http://cardo-ua.com/platya/1041-sarafan-valio-babochki-leto-2015.html
Не отображать!ID = 11041
URL = http://cardo-ua.com/platya/1040-plate-vida-darck-blue-leto-2015.html
Не отображать!ID = 11042
URL = http://cardo-ua.com/platya/1039-plate-vida-white-leto-2015.html
Не отображать!ID = 11043
URL = http://cardo-ua.com/platya/1038-plate-blanik-babochki-leto-2015.html
Не отображать!ID = 11044
URL = http://cardo-ua.com/platya/1037-plate-ellada-black-leto-2015.html
Не отображать!ID = 11045
URL = http://cardo-ua.com/platya/1036-plate-peru-blue-leto-2015.html
Не отображать!ID = 11046
URL = http://cardo-ua.com/platya/1035-plate-angel-blue-leto-2015.html
Не отображать!ID = 11047
URL = http://cardo-ua.com/platya/1034-plate-angel-red-leto-2015.html
Не отображать!ID = 11048
URL = http://cardo-ua.com/platya/1033-plate-angel-white-leto-2015.html
Не отображать!ID = 11049
URL = http://cardo-ua.com/platya/1032-plate-karmen-blue-leto-2015.html
Не отображать!ID = 11050
URL = http://cardo-ua.com/platya/1031-plate-karmen-red-leto-2015.html
Не отображать!ID = 11051
URL = http://cardo-ua.com/platya/1030-plate-ivora-blue-leto-2015.html
Не отображать!ID = 11052
URL = http://cardo-ua.com/platya/1029-plate-ivora-yellow-leto-2015.html
Не отображать!ID = 11053
URL = http://cardo-ua.com/ubki/1028-shorty-fox-black-leto2015.html
Не отображать!ID = 11054
URL = http://cardo-ua.com/platya/1027-plate-darsi-black-leto-2015.html
Не отображать!ID = 11055
URL = http://cardo-ua.com/platya/1026-letnee-plate-margi-red-leto-2015.html
Не отображать!ID = 11056
URL = http://cardo-ua.com/platya/1025-letnee-plate-margi-green-leto-2015.html
Не отображать!ID = 11057
URL = http://cardo-ua.com/platya/1024-plate-marsi-melkiy-tsvetochek-na-belom-leto-2015.html
Не отображать!ID = 11058
URL = http://cardo-ua.com/platya/1023-plate-marsi-belaja-hohloma-leto-2015.html
Не отображать!ID = 11059
URL = http://cardo-ua.com/platya/1022-plate-magnolia-peach-leto-2015.html
Не отображать!ID = 11061
URL = http://cardo-ua.com/kombinezoni/1020-kombinezon-arin-fantazija-leto-2015.html
Не отображать!ID = 11062
URL = http://cardo-ua.com/kombinezoni/1019-kombinezon-bi-gi-blue-pion-leto-2015.html
Не отображать!ID = 11063
URL = http://cardo-ua.com/kombinezoni/1018-kombinezon-bi-gi-light-gray-leto-2015.html
Не отображать!ID = 11064
URL = http://cardo-ua.com/bluzi/1017-top-s-baskoy-defin-kletka-blue-leto2015.html
Не отображать!ID = 11065
URL = http://cardo-ua.com/bluzi/1016-top-s-baskoy-defin-kletka-red-leto2015.html
Не отображать!ID = 11066
URL = http://cardo-ua.com/kombinezoni/1015-kombinezon-axel-gray-leto-2015.html
Не отображать!ID = 11067
URL = http://cardo-ua.com/kombinezoni/1014-kombinezon-axel-plum-leto-2015.html
Не отображать!ID = 11068
URL = http://cardo-ua.com/ubki/1013-ubka-optima-black-leto2015.html
Не отображать!ID = 11069
URL = http://cardo-ua.com/platya/1012-plate-ivora-peach-leto-2015.html
Не отображать!ID = 11070
URL = http://cardo-ua.com/platya/1011-plate-ivora-mint-leto-2015.html
Не отображать!ID = 11071
URL = http://cardo-ua.com/platya/935-plate-pesto-blackbordo-vesna-leto-2015.html
Не отображать!ID = 11072
URL = http://cardo-ua.com/919-bluza-top-rossi-berry-vesna-leto2015.html
Не отображать!ID = 11073
URL = http://cardo-ua.com/platya/903-plate-vio-lilovyy-vesna-leto-2015.html
Не отображать!ID = 11074
URL = http://cardo-ua.com/901-plate-flower-vesna-leto-2015.html
Не отображать!ID = 11075
URL = http://cardo-ua.com/platya/894-plate-sfera-blue-vesna-leto-2015.html
Не отображать!ID = 11076
URL = http://cardo-ua.com/platya/893-plate-alex-black-vesna-2015.html
Не отображать!ID = 11077
URL = http://cardo-ua.com/platya/892-plate-zegna-pletenie-vesna-2015.html
Не отображать!ID = 11078
URL = http://cardo-ua.com/zenskaya-odezda/891-kurtka-bomber-real-melangeblack-vesna-2015.html
Не отображать!ID = 11079
URL = http://cardo-ua.com/889-shorty-bios-koja-vesna-leto2015.html
Не отображать!ID = 11080
URL = http://cardo-ua.com/888-kurtka-bomber-sentra-tvidkoja-vesna-2015.html
Не отображать!ID = 11081
URL = http://cardo-ua.com/platya/887-plate-mirta-temno-siniy-vesna-leto-2015.html
Не отображать!ID = 11082
URL = http://cardo-ua.com/platya/886-plate-desert-blue-vesna-leto-2015.html
Не отображать!ID = 11083
URL = http://cardo-ua.com/885-plate-desert-coral-vesna-leto-2015.html
Не отображать!ID = 11084
URL = http://cardo-ua.com/platya/884-plate-desert-mint-vesna-leto-2015.html
Не отображать!ID = 11085
URL = http://cardo-ua.com/platya/883-komplekt-sigma-dark-blue-gljanets-zima-2015.html
Не отображать!ID = 11086
URL = http://cardo-ua.com/platya/882-plate-dolly-milky-vesna-leto-2015.html
Не отображать!ID = 11087
URL = http://cardo-ua.com/881-bluza-kimo-black-flower-vesna-leto2015.html
Не отображать!ID = 11088
URL = http://cardo-ua.com/platya/880-plate-mirta-persik-vesna-leto-2015.html
Не отображать!ID = 11089
URL = http://cardo-ua.com/879-plate-margaret-vesna-leto-2015.html
Не отображать!ID = 11090
URL = http://cardo-ua.com/878-plate-sfera-mint-vesna-leto-2015.html
Не отображать!ID = 11091
URL = http://cardo-ua.com/877-bruki-eclipse-seryy-vesna-2015.html
Не отображать!ID = 11092
URL = http://cardo-ua.com/876-bluza-beka-corallblue-vesna-2015.html
Не отображать!ID = 11093
URL = http://cardo-ua.com/platya/875-komplekt-regina-yellow-vesna-2015.html
Не отображать!ID = 11094
URL = http://cardo-ua.com/platya/874-plate-xena-pudra-vesna-2015.html
Не отображать!ID = 11095
URL = http://cardo-ua.com/platya/873-plate-xena-gusinaja-lapka-vesna-2015.html
Не отображать!ID = 11096
URL = http://cardo-ua.com/platya/872-plate-xena-mint-vesna-2015.html
Не отображать!ID = 11097
URL = http://cardo-ua.com/bluzi/871-bluza-violin-gray-vesna-2015.html
Не отображать!ID = 11098
URL = http://cardo-ua.com/bluzi/870-rubashka-navi-purple-vesna-2015.html
Не отображать!ID = 11099
URL = http://cardo-ua.com/bluzi/869-rubashka-navi-pink-vesna-2015.html
Не отображать!ID = 11100
URL = http://cardo-ua.com/bluzi/868-bluza-beka-blackwhite-vesna-2015.html
Не отображать!ID = 11101
URL = http://cardo-ua.com/bluzi/867-bluza-beka-bluebeige-vesna-2015.html
Не отображать!ID = 11102
URL = http://cardo-ua.com/866-rubashka-viland-white-vesna-2015.html
Не отображать!ID = 11103
URL = http://cardo-ua.com/bluzi/865-bluzka-verona-blackwhite-vesna-2015.html
Не отображать!ID = 11104
URL = http://cardo-ua.com/platya/864-plate-frida-peach-vesna-2015.html
Не отображать!ID = 11105
URL = http://cardo-ua.com/863-kombinezon-interi-bordo-vesna-2015.html
Не отображать!ID = 11106
URL = http://cardo-ua.com/862-plate-alex-beige-capuccino-vesna-2015.html
Не отображать!ID = 11107
URL = http://cardo-ua.com/861-kurtka-bomber-real-blackmelange-vesna-2015.html
Не отображать!ID = 11108
URL = http://cardo-ua.com/860-plate-mirta-malahit-vesna-2015.html
Не отображать!ID = 11109
URL = http://cardo-ua.com/859-plate-zegna-lepestki-vesna-2015.html
Не отображать!ID = 11110
URL = http://cardo-ua.com/858-bruki-pola-seryy-vesna-2015.html
Не отображать!ID = 11111
URL = http://cardo-ua.com/857-plate-dita-black-vesna-2015.html
Не отображать!ID = 11112
URL = http://cardo-ua.com/856-plate-xena-yellow-vesna-2015.html
Не отображать!ID = 11113
URL = http://cardo-ua.com/855-sarafan-neo-blue-vesna-2015.html
Не отображать!ID = 11114
URL = http://cardo-ua.com/zenskaya-odezda/853-futbolka-glam-milk-vesna-2015.html
Не отображать!ID = 11115
URL = http://cardo-ua.com/platya/852-plate-amanda-capuccino-vesna-2015.html
Не отображать!ID = 11116
URL = http://cardo-ua.com/kombinezoni/851-kombinezon-dora-sea-green-vesna-2015.html
Не отображать!ID = 11117
URL = http://cardo-ua.com/kombinezoni/850-kombinezon-dora-chocolate-vesna-2015.html
Не отображать!ID = 11118
URL = http://cardo-ua.com/849-kombinezon-dora-black-vesna-2015.html
Не отображать!ID = 11119
URL = http://cardo-ua.com/platya/848-plate-viora-black-vesna-2015.html
Не отображать!ID = 11120
URL = http://cardo-ua.com/847-plate-viora-vine-vesna-2015.html
Не отображать!ID = 11121
URL = http://cardo-ua.com/platya/846-plate-viktoria-black-vesna-2015.html
Не отображать!ID = 11122
URL = http://cardo-ua.com/platya/845-plate-viktoria-bordo-vesna-2015.html
Не отображать!ID = 11123
URL = http://cardo-ua.com/platya/844-plate-gven-mint-vesna-2015.html
Не отображать!ID = 11124
URL = http://cardo-ua.com/platya/843-plate-brenda-coffee-vesna-2015.html
Не отображать!ID = 11125
URL = http://cardo-ua.com/platya/842-plate-letta-vine-vesna-2015.html
Не отображать!ID = 11126
URL = http://cardo-ua.com/platya/841-plate-letta-sea-green-vesna-2015.html
Не отображать!ID = 11127
URL = http://cardo-ua.com/840-plate-dolly-malahit-vesna-2015.html
Не отображать!ID = 11128
URL = http://cardo-ua.com/platya/839-plate-dolly-mint-vesna-2015.html
Не отображать!ID = 11129
URL = http://cardo-ua.com/platya/838-plate-gven-berry-vesna-2015.html
Не отображать!ID = 11130
URL = http://cardo-ua.com/837-plate-viktoria-beige-vesna-2015.html
Не отображать!ID = 11131
URL = http://cardo-ua.com/834-plate-ride-vesna-2015.html
Не отображать!ID = 11132
URL = http://cardo-ua.com/833-bluza-violin-dark-blue-vesna-2015.html
Не отображать!ID = 11133
URL = http://cardo-ua.com/832-rubashka-navi-sky-blue-vesna-2015.html
Не отображать!ID = 11134
URL = http://cardo-ua.com/831-shorty-fox-beige-vesna2015.html
Не отображать!ID = 11135
URL = http://cardo-ua.com/830-futbolka-glam-black-vesna-2015.html
Не отображать!ID = 11136
URL = http://cardo-ua.com/829-plate-marika-vesna-2015.html
Не отображать!ID = 11142
URL = http://cardo-ua.com/822-bluzka-verona-goroh-vesna-2015.html
Не отображать!ID = 11143
URL = http://cardo-ua.com/821-plate-skip-blue-vesna-2015.html
Не отображать!ID = 11144
URL = http://cardo-ua.com/820-plate-brenda-black-vesna-2015.html
Не отображать!ID = 11145
URL = http://cardo-ua.com/platya/819-plate-bojole-black-vesna-2015.html
Не отображать!ID = 11146
URL = http://cardo-ua.com/818-plate-bojole-vine-vesna-2015.html
Не отображать!ID = 11147
URL = http://cardo-ua.com/platya/816-plate-viven-beige-skin-vesna-2015.html
Не отображать!ID = 11148
URL = http://cardo-ua.com/bluzi/815-bluza-lady-berry-vesna-2015.html
Не отображать!ID = 11149
URL = http://cardo-ua.com/bluzi/814-bluza-lady-mint-vesna-2015.html
Не отображать!ID = 11150
URL = http://cardo-ua.com/bluzi/813-bluza-lady-dark-blue-vesna-2015.html
Не отображать!ID = 11151
URL = http://cardo-ua.com/ubki/810-ubka-zoom-rozy-vesna2015.html
Не отображать!ID = 11152
URL = http://cardo-ua.com/zenskaya-odezda/809-tunika-era-black-vesna-2015.html
Не отображать!ID = 11153
URL = http://cardo-ua.com/zenskaya-odezda/808-kofta-zoom-rozy-vesna-2015.html
Не отображать!ID = 11154
URL = http://cardo-ua.com/platya/807-plate-estel-peach-vesna-2015.html
Не отображать!ID = 11155
URL = http://cardo-ua.com/bruki/806-leginsy-basic-fioletovyy-buket-vesna-2015.html
Не отображать!ID = 11156
URL = http://cardo-ua.com/zenskaya-odezda/805-kofta-basic-fioletovyy-buket-vesna-2015.html
Не отображать!ID = 11157
URL = http://cardo-ua.com/bluzi/804-rubashka-nensi-lila-vesna-2015.html
Не отображать!ID = 11158
URL = http://cardo-ua.com/803-rubashka-nensi-red-vesna-2015.html
Не отображать!ID = 11161
URL = http://cardo-ua.com/800-bluza-gamma-bej-vesna-2015.html
Не отображать!ID = 11162
URL = http://cardo-ua.com/platya/799-plate-estel-black-vesna-2015.html
Не отображать!ID = 11163
URL = http://cardo-ua.com/platya/798-plate-estel-mint-vesna-2015.html
Не отображать!ID = 11164
URL = http://cardo-ua.com/platya/797-plate-paola-capuccino-vesna-2015.html
Не отображать!ID = 11166
URL = http://cardo-ua.com/platya/795-plate-welly-black-vesna-2015.html
Не отображать!ID = 11167
URL = http://cardo-ua.com/platya/794-plate-welly-bordo-vesna-2015.html
Не отображать!ID = 11168
URL = http://cardo-ua.com/platya/793-plate-ketrin-fiolet-vesna-2015.html
Не отображать!ID = 11169
URL = http://cardo-ua.com/platya/792-plate-ketrin-morskaja-volna-vesna-2015.html
Не отображать!ID = 11170
URL = http://cardo-ua.com/platya/791-plate-legend-izumrud-vesna-2015.html
Не отображать!ID = 11171
URL = http://cardo-ua.com/790-ubka-pillar-black-vesna2015.html
Не отображать!ID = 11172
URL = http://cardo-ua.com/789-plate-viven-black-skin-vesna-2015.html
Не отображать!ID = 11173
URL = http://cardo-ua.com/788-plate-estel-violet-vesna-2015.html
Не отображать!ID = 11174
URL = http://cardo-ua.com/787-plate-nissa-goroh-vesna-2015.html
Не отображать!ID = 11175
URL = http://cardo-ua.com/786-plate-ketrin-red-vesna-2015.html
Не отображать!ID = 11176
URL = http://cardo-ua.com/785-plate-paola-black-vesna-2015.html
Не отображать!ID = 11177
URL = http://cardo-ua.com/784-plate-welly-peach-vesna-2015.html
Не отображать!ID = 11178
URL = http://cardo-ua.com/783-plate-legend-black-vesna-2015.html
Не отображать!ID = 11179
URL = http://cardo-ua.com/782-tunika-era-tsvety-bordo-vesna-2015.html
Не отображать!ID = 11181
URL = http://cardo-ua.com/780-plate-kira-blackmint-vesna-2015.html
Не отображать!ID = 11182
URL = http://cardo-ua.com/platya/777-plate-adel-gusinaja-lapka-vesna-2015.html
Не отображать!ID = 11183
URL = http://cardo-ua.com/platya/776-plate-emma-blackrose-vesna-2015.html
Не отображать!ID = 11184
URL = http://cardo-ua.com/platya/774-plate-fema-piton-temno-siniy-vesna-2015.html
Не отображать!ID = 11185
URL = http://cardo-ua.com/platya/773-plate-fema-piton-bej-vesna-2015.html
Не отображать!ID = 11186
URL = http://cardo-ua.com/platya/772-plate-nelli-mint-vesna-2015.html
Не отображать!ID = 11187
URL = http://cardo-ua.com/platya/771-plate-nelli-beige-vesna-2015.html
Не отображать!ID = 11188
URL = http://cardo-ua.com/ubki/768-ubka-muskus-mint-vesna2015.html
Не отображать!ID = 11189
URL = http://cardo-ua.com/767-ubka-muskus-black-vesna2015.html
Не отображать!ID = 11190
URL = http://cardo-ua.com/765-komplekt-roxy-mint-vesna-2015.html
Не отображать!ID = 11191
URL = http://cardo-ua.com/762-bluza-lady-beige-vesna-2015.html
Не отображать!ID = 11192
URL = http://cardo-ua.com/761-plate-kira-blackwhite-vesna-2015.html
Не отображать!ID = 11193
URL = http://cardo-ua.com/760-plate-adel-v-goroshek-vesna-2015.html
Не отображать!ID = 11194
URL = http://cardo-ua.com/759-plate-space-melange-vesna-2015.html
Не отображать!ID = 11195
URL = http://cardo-ua.com/758-plate-nelli-black-vesna-2015.html
Не отображать!ID = 11196
URL = http://cardo-ua.com/757-plate-fema-piton-mentol-vesna-2015.html
Не отображать!ID = 11197
URL = http://cardo-ua.com/756-komplekt-roxy-dark-blue-vesna-2015.html
Не отображать!ID = 11198
URL = http://cardo-ua.com/753-leginsy-basic-zelenyy-buket-vesna-2015.html
Не отображать!ID = 11199
URL = http://cardo-ua.com/752-ubka-pils-zipp-vesna2015.html
Не отображать!ID = 11200
URL = http://cardo-ua.com/751-ubka-hilton-chernyy-gipur-vesna2015.html
Не отображать!ID = 11201
URL = http://cardo-ua.com/750-ubka-zoom-tsvety-persik-vesna2015.html
Не отображать!ID = 11202
URL = http://cardo-ua.com/749-kofta-basic-zelenyy-buket-vesna-2015.html
Не отображать!ID = 11203
URL = http://cardo-ua.com/745-plate-gia-mint-green-vesna-2015.html
Не отображать!ID = 11204
URL = http://cardo-ua.com/742-plate-sandi-bordo-vesna2015.html
Не отображать!ID = 11205
URL = http://cardo-ua.com/740-plate-dolly-malahit-vesna-2015.html
Не отображать!ID = 11206
URL = http://cardo-ua.com/739-plate-favola-kletka-red-vesna2015.html
Не отображать!ID = 11207
URL = http://cardo-ua.com/735-plate-sandy-beige-vesna-2015.html
Не отображать!ID = 11208
URL = http://cardo-ua.com/734-plate-tiamo-v-goroshek-vesna-2015.html
Не отображать!ID = 11209
URL = http://cardo-ua.com/733-plate-fler-kletka-red-vesna-2015.html
Не отображать!ID = 11210
URL = http://cardo-ua.com/731-komplekt-regina-peach-vesna-2015.html
Не отображать!ID = 11274
URL = http://cardo-ua.com/430-plate-ringo-black-osen-2014.html
Не отображать!ID = 11276
URL = http://cardo-ua.com/426-shorty-art-print-tsvety-osen-2014.html
Не отображать!ID = 11277
URL = http://cardo-ua.com/419-pidjak-denfi-bejevyy-osen-2014.html
Не отображать!ID = 11278
URL = http://cardo-ua.com/409-rubashka-paloma-chernyy.html
Не отображать!ID = 11279
URL = http://cardo-ua.com/407-rubashka-gianni-sinie-tsvety.html
Не отображать!ID = 11280
URL = http://cardo-ua.com/406-rubashka-gianni.html
Не отображать!ID = 11281
URL = http://cardo-ua.com/394-top-bona-3d-leto-2014.html
Не отображать!ID = 11282
URL = http://cardo-ua.com/391-top-bona-.html
Не отображать!ID = 11283
URL = http://cardo-ua.com/380-plate-lutens-mozaika-biruza.html
Не отображать!ID = 11284
URL = http://cardo-ua.com/367-ubka-scarlet-belaja-leto-2014.html
Не отображать!ID = 11285
URL = http://cardo-ua.com/366-plate-lutens-sinjaja-mozaika.html
Не отображать!ID = 11287
URL = http://cardo-ua.com/361-plate-brizz-leopard-oranj.html
Не отображать!ID = 11288
URL = http://cardo-ua.com/360-plate-brizz-leopard-malina.html
Не отображать!ID = 11289
URL = http://cardo-ua.com/platya/357-plate-malena-goluboe-nebo.html
Не отображать!ID = 11290
URL = http://cardo-ua.com/bruki/340-bruki-loran-limonnyy-mus-leto-2014.html
Не отображать!ID = 11291
URL = http://cardo-ua.com/platya/325-letnee-plate-ameli-goluboe.html
Не отображать!ID = 11292
URL = http://cardo-ua.com/ubki/313-shorty-biz-solnechnogo.html
Не отображать!ID = 11293
URL = http://cardo-ua.com/ubki/312-shorty-biz-persikovyy-leto-2014.html
Не отображать!ID = 11298
URL = http://cardo-ua.com/bluzi/79-bluza-jenskaya-krep-shifon.html
Не отображать!ID = 11299
URL = http://cardo-ua.com/platya/73-sinee-plate-s-bantom.html
Не отображать!ID = 11300
URL = http://cardo-ua.com/platya/70-plate-sarafan-viskoza.html
Не отображать!ID = 11301
URL = http://cardo-ua.com/platya/69-rozovoe-plate-sarafan-viskoza.html
Не отображать!ID = 11302
URL = http://cardo-ua.com/platya/68-chernoe-plate-sarafan.html
Не отображать!ID = 11303
URL = http://cardo-ua.com/platya/67-chernoe-plate-sarafan-viskoza.html
Не отображать!ID = 11304
URL = http://cardo-ua.com/platya/66-chernoe-plate-sarafan-viskoza.html
Не отображать!ID = 11305
URL = http://cardo-ua.com/platya/65-chernoe-plate-sarafan.html
Не отображать!ID = 11314
URL = http://cardo-ua.com/platya/41-plate-atlas-cotton.html
Не отображать!ID = 11315
URL = http://cardo-ua.com/platya/1266-plate-essi-temno-sinee-jasmin-leto-2015.html
Не отображать!ID = 11316
URL = http://cardo-ua.com/platya/1265-plate-mare-temno-sinjaja-poloska-leto-2015.html
Не отображать!ID = 11317
URL = http://cardo-ua.com/platya/1264-plate-satin-krasnoe-jasmin-leto-2015.html
Не отображать!ID = 11318
URL = http://cardo-ua.com/platya/1263-plate-satin-temno-sinee-jasmin-leto-2015.html
Не отображать!ID = 11594
URL = http://cardo-ua.com/platya/1273-plate-natali-tsvety-leto-2015.html
Не отображать!ID = 11595
URL = http://cardo-ua.com/platya/1272-plate-natali-temno-sinee-v-goroshek-leto-2015.html
Не отображать!ID = 11596
URL = http://cardo-ua.com/platya/1271-plate-angelika-mentol-v-goroshek-leto-2015.html
Не отображать!ID = 11597
URL = http://cardo-ua.com/platya/1270-plate-angelika-krasnoe-v-goroshek-leto-2015.html
Не отображать!ID = 11598
URL = http://cardo-ua.com/platya/1269-plate-anika-goluboy-leto-2015.html
Не отображать!ID = 11599
URL = http://cardo-ua.com/platya/1268-plate-anika-jeltyy-leto-2015.html
Не отображать!ID = 11600
URL = http://cardo-ua.com/platya/1267-plate-anika-moloko-leto-2015.html
Не отображать!ID = 11601
URL = http://cardo-ua.com/platya/1211-plate-satin-krupnye-tsvety-leto-2015.html
Не отображать!ID = 11602
URL = http://cardo-ua.com/platya/1199-plate-cosmo-temno-sinjaja-poloska-leto-2015.html
Не отображать!ID = 11603
URL = http://cardo-ua.com/platya/1178-plate-dali-temno-sinee-leto-2015.html
Не отображать!ID = 11604
URL = http://cardo-ua.com/platya/1146-plate-arden-nebesnyy-sad-leto-2015.html
Не отображать!ID = 11605
URL = http://cardo-ua.com/platya/1126-plate-arden-oazis-leto-2015.html
Не отображать!ID = 11606
URL = http://cardo-ua.com/platya/1117-plate-elixir-beloe-leto-2015.html
Не отображать!ID = 11608
URL = http://cardo-ua.com/678-plate-lorian-beige-zima-2015.html
Не отображать!ID = 11892
URL = http://cardo-ua.com/platya/1305-plate-pandora-temno-seroe-leto-2015.html
Не отображать!ID = 11893
URL = http://cardo-ua.com/platya/1303-plate-vendy-gorchitsa-tsvety-leto-2015.html
Не отображать!ID = 11894
URL = http://cardo-ua.com/platya/1302-plate-elite-serye-tsvety-leto-2015.html
Не отображать!ID = 11895
URL = http://cardo-ua.com/platya/1301-plate-elite-korall-v-tsvetochek-leto-2015.html
Не отображать!ID = 11896
URL = http://cardo-ua.com/platya/1300-plate-delia-beloe-v-goroshek-leto-2015.html
Не отображать!ID = 11897
URL = http://cardo-ua.com/platya/1299-plate-natali-beloe-v-tsvetochek-leto-2015.html
Не отображать!ID = 11899
URL = http://cardo-ua.com/platya/1297-plate-toffi-hohloma-leto-2015.html
Не отображать!ID = 11900
URL = http://cardo-ua.com/platya/1296-plate-klio-tsvety-leto-2015.html
Не отображать!ID = 11901
URL = http://cardo-ua.com/platya/1295-plate-klio-nebesnyy-sad-leto-2015.html
Не отображать!ID = 11902
URL = http://cardo-ua.com/platya/1294-plate-over-chernaja-kletka-leto-2015.html
Не отображать!ID = 11903
URL = http://cardo-ua.com/platya/1293-plate-oxy-lugovye-tsvety-salat-leto-2015.html
Не отображать!ID = 11904
URL = http://cardo-ua.com/platya/1292-plate-extra-bejevoe-leto-2015.html
Не отображать!ID = 11905
URL = http://cardo-ua.com/platya/1291-sarafan-agilera-temno-sinee-v-melkuu-orhideu-leto-2015.html
Не отображать!ID = 11906
URL = http://cardo-ua.com/platya/1290-sarafan-agilera-bej-v-melkuu-orhideu-leto-2015.html
Не отображать!ID = 11907
URL = http://cardo-ua.com/platya/1289-plate-essi-roza-fistashka-leto-2015.html
Не отображать!ID = 11909
URL = http://cardo-ua.com/platya/1287-sarafan-star-zelenyy-romashka-leto-2015.html
Не отображать!ID = 11910
URL = http://cardo-ua.com/platya/1286-sarafan-star-krasnyy-romashka-leto-2015.html
Не отображать!ID = 11911
URL = http://cardo-ua.com/platya/1285-sarafan-star-siniy-romashka-leto-2015.html
Не отображать!ID = 11912
URL = http://cardo-ua.com/platya/1284-plate-over-krasnaja-kletka-leto-2015.html
Не отображать!ID = 11913
URL = http://cardo-ua.com/platya/1283-plate-extra-jeltoe-leto-2015.html
Не отображать!ID = 11914
URL = http://cardo-ua.com/platya/1282-sarafan-agilera-temno-siniy-v-melkiy-tsvetok-leto-2015.html
Не отображать!ID = 11915
URL = http://cardo-ua.com/platya/1281-plate-vega-serye-maki-leto-2015.html
Не отображать!ID = 11916
URL = http://cardo-ua.com/platya/1280-plate-esta-bejevoe-leto-2015.html
Не отображать!ID = 11917
URL = http://cardo-ua.com/platya/1279-plate-ninel-temno-siniy-v-goroh-leto-2015.html
Не отображать!ID = 11919
URL = http://cardo-ua.com/platya/1277-plate-elite-jeltoe-v-tsvetochek-leto-2015.html
Не отображать!ID = 11920
URL = http://cardo-ua.com/platya/1276-plate-delia-temno-sinee-v-belyy-goroshek-leto-2015.html
Не отображать!ID = 11921
URL = http://cardo-ua.com/platya/1275-plate-oxy-lugovye-tsvety-bej-leto-2015.html
Не отображать!ID = 11922
URL = http://cardo-ua.com/platya/1274-plate-extra-moloko-leto-2015.html
Не отображать!ID = 11932
URL = http://cardo-ua.com/platya/1247-plate-amur-temno-sinee-s-venzeljami-leto-2015.html';
        $pat = '/ID = ([\d]{4,5})/';
        preg_match_all($pat, $sthr, $ids);
        $aaa = $ids[1];
        $ccc = '';
        foreach ($aaa as $bbb) {
            $ccc .=$bbb.', ';
        }
        $ccc = substr($ccc, 0, strlen($ccc)- 2);
        echo $ccc;
        $sql = "UPDATE `shop_commodity` SET `commodity_visible`=1
WHERE `commodity_ID` in ($ccc)";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            echo 'yes';
            return self::MSG_SUCCESS;
        } else {
            echo 'no';
            return self::MSG_NOT_SUCCESS;
        }
    }

    public function getIdss()
    {
        $sql = "SELECT `commodity_ID` as id FROM `shop_commodity` as c
INNER JOIN  `shop_commodities-categories` as cc
ON cc.commodityID = c.`commodity_ID`
WHERE cc.categoryID = 1
and `commodity_ID` > 8000
and `com_sizes` <> 'XL(50)'";
        $aaaa = '';
        $stmt = $this->dbh->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $aaaa .= $row['id'].", ";
        }
        $aaaa = substr($aaaa, 0, strlen($aaaa)- 2);
        echo $aaaa;
        $sql = "UPDATE `shop_commodity` SET `commodity_visible`=1
WHERE `commodity_ID` in ($aaaa)";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            echo 'yes';
            return self::MSG_SUCCESS;
        } else {
            echo 'no';
            return self::MSG_NOT_SUCCESS;
        }
    }
}