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
    const DSN = 'mysql:dbname=******;host=******';
    const USER = '*******';
    const PASSWORD = '******';
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
        $this->quantities = $this->getAllQuantity();
    }

    public function getUrls()
    {
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
            return self::MSG_NOT_SUCC_SIZE;
        }
    }

    public function addQuantity($id, $size, $quantity)
    {
        $size = $this->dbh->quote($size);
        $sql = "INSERT INTO `shop_cardo_sizes`(`commodity_id`, `size`, `quantity`)
VALUES ($id, $size, $quantity)";
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
            return self::MSG_SUCC_QUANT;
        } else {
            $this->dbh = new \PDO(self::DSN, self::USER, self::PASSWORD);
            return self::MSG_NOT_SUCC_QUANT;
        }
    }

    public function updateQuantity($id, $size, $quantity)
    {
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

    public function getAllQuantity()
    {
        $sql = "SELECT 	commodity_id, size, quantity
FROM  `shop_cardo_sizes` ";
        $stmt = $this->dbh->query($sql);
        $sizes = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sizes[] = $row;
        }
        return $sizes;
    }

    /**
     * Check if exist size in database
     * @param Int $id , String $name of size
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

    public function checkActualSizes($id, $sizes)
    {
        if ($this->allSizes[$id] == $sizes) {
            return true;
        } else {
            return false;
        }
    }

    public function setInterface($count, $step, $updated, $result, $begin, $hide)
    {
        echo $step . "up";
        echo $count . "cou";
        $a = $count / 100;
        $a2 = round($step / $a, 2);
        $andSql = "";
        if ($step == 1) {
            $andSql .= ", start_time='{$begin}'";
            $andSql .= $updated ? ", update_add = 1" : ", update_add = 0";
            $andSql .= $hide ? ", par_hide = 1" : ", par_hide = 0";
        } elseif ($updated || $hide) {
            $andSql .= $updated ? ', `update_add`=`update_add`+1' : '';
            $andSql .= $hide ? ', `par_hide`=`par_hide`+1' : '';
        }
        $result = $this->dbh->quote($result);
        $sql = "
UPDATE parser_interface
SET update_prog='{$a2}', check_prog='{$step}', text={$result}{$andSql}
WHERE par_id='5'
";
        $this->dbh->query('SET names utf8');
        $stmt = $this->dbh->exec($sql);
        if ($stmt > 0) {
//            echo "Good";
        } else {
            echo "Not Good";
        }
    }

    public function setInterfaceComplete($result, $today)
    {
        $sql = "
UPDATE parser_interface
SET text='{$result}', update_date='{$today}', update_prog='100'
WHERE par_id='5'
";
        $this->dbh->query('SET names utf8');
        $this->dbh->exec($sql);
    }

    public function getUpdated()
    {
        $sql = "
SELECT  update_add, par_hide
FROM  `parser_interface`
WHERE `par_id`='5'";
        $stmt = $this->dbh->query($sql);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            return array($row['update_add'], $row['par_hide']);
        } else {
            return false;
        }
    }

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

    public function checkActualOptPrices($id, $price)
    {
        if ($this->allOptPrices[$id] == $price) {
            return true;
        } else {
            return false;
        }
    }
}

