<?php
/**
 * Created by PhpStorm.
 * User: volodini
 * Date: 6/20/15
 * Time: 3:13 PM
 */

namespace Makewear;

use Nearsoft\SeleniumClient\SelectElement;
use Nearsoft\SeleniumClient\WebDriver;
use Nearsoft\SeleniumClient\By;
use Nearsoft\SeleniumClient\DesiredCapabilities;
use DateTime;

class Cardo
{
    const PATTERN = '/только\s([\d]{1,2})шт/';
    const NUMBER = 999;
    const ID_INPUT = "quantity_wanted";
    const ID_QT_WARNING = "qtyWarning";
    const ID_SELECT = "group_4";
    const XPATH = "//select[@id='group_4']/option";
    const MSG_SIZE_ACTUAL = "Кількість актуальна!";
    const GOOGLE = "http://www.google.com.ua";
    const ID_CHECK = "sfopt";

    private $driver;
    private $urls;
    private $allItems = array();
    private $select;
    private $connectDb;
    private $begin;
    private $end;
    public $updated = false;
    public $count;
    public $result = '';
    public $hide = 0;
    public $step;
    public $login = false;

    public function __construct($connectDb)
    {
        $this->begin = new \DateTime('now');
        $capabilities = new DesiredCapabilities('firefox');
        $this->driver = new WebDriver($capabilities);
        $this->checkSeleniumServer();
        $this->connectDb = $connectDb;
        $this->urls = $this->connectDb->getUrls();
        $this->count = count($this->urls);
        $this->logIn();
//        $this->urls = [
//            5939 => "http://cardo-ua.com/platya/942-plate-milan-butterfly-vesna-leto-2015.html"
//        ];
    }

    /**
     * Login.
     * Sets $this->login true if login was success.
     **/
    private function logIn()
    {
        $this->driver->get("http://cardo-ua.com/authentication?back=my-account.php");
        $emails = $this->driver->findElements(By::id('email'));
        $pwds = $this->driver->findElements(By::id('passwd'));
        $submits = $this->driver->findElements(By::id('SubmitLogin'));
        if (count($emails) > 0 && count($pwds) > 0 && count($submits) > 0) {
            $email = $emails[0];
            $email->sendKeys('v.chupovsky@makewear.com.ua');
            $pwd = $pwds[0];
            $pwd->sendKeys('qqqqqqqqq');
            $submit = $submits[0];
            $submit->click();
        }
        sleep(3);
        $this->driver->getCookies();
        $names = $this->driver->findElementsByXPath('//div[@id="header_user_info"]/span/a');
        $name = $names[0];
        if (count($names) > 0) {
            if (strpos($name->getText(), 'лоди')) {
                $this->login = true;
                echo "login";
            } else {
                echo "not login";
            }
        }
    }

    /**
     * Get opt price.
     * @return string.
     **/
    private function getOptPrice()
    {
        $prices = $this->driver->findElementsByXPath("//span[@id='our_price_display']");
        if (count($prices) > 0) {
            $price = $prices[0];
            preg_match('/\d+/', $price->getText(), $matches);
            return $matches[0];
        }
    }

    /***
     * Checks or is running chromedriver on computer.
     * @throws \Nearsoft\SeleniumClient\Exceptions\InvalidSelector
     */
    private function checkSeleniumServer()
    {
        $this->driver->get(self::GOOGLE);
        $webCheck = $this->driver->findElements(By::id(self::ID_CHECK));
        if (count($webCheck) > 0) {
            echo "<h1>OK!</h1>";
        } else {
            echo "<h1>STATR SELENIUM SERVER OR CONNECT TO INTERNET!</h1>";
            exit;
        }
    }

    public function getAllSizes($url)
    {
        $this->driver->get($url);
        usleep(500000);
        $webSelect = $this->driver->findElements(By::id(self::ID_SELECT));
        if (count($webSelect) > 0) {
            $this->select = new SelectElement($this->driver->findElementById(self::ID_SELECT));
            return $this->driver->findElementsByXPath(self::XPATH);
        } else {
            return false;
        }
    }

    /***
     * in this function 2 times checks visibiliti INPUT because was bugs!!!!!!!!
     * @param $webOption
     * @return array
     * @throws \Nearsoft\SeleniumClient\Exceptions\InvalidSelector
     */
    private function getQuantity($webOption)
    {
        $item = array();
        $item[] = $webOption->getAttribute("title");
        $item['quantity'] = 0;
        $this->select->selectByPartialText($webOption->getText());
        sleep(1);
        $check = $this->driver->findElements(By::id(self::ID_INPUT))[0];
        if (!$check->isDisplayed()) {
            sleep(1);
        }
        if (!$check->isDisplayed()) {
            sleep(1);
        }
        if ($check->isDisplayed()) {
            if ($check->isDisplayed()) {
                $check->sendKeys(self::NUMBER);
                sleep(1);
                $warningFind = $this->driver->findElements(By::id(self::ID_QT_WARNING))[0];
                if ($warningFind->isDisplayed()) {
                    $warning = $warningFind->getText();
                    preg_match(self::PATTERN, $warning, $available);
                    if (count($available > 1)) {
                        $item['quantity'] = $available[1];
                    }
                }
            }
        }
        return $item;
    }

    public function getAllItems()
    {
        foreach ($this->urls as $id => $url) {
//            if ($id <  11103) continue;
//            if (!$this->connectDb->checkActualOptPrices($id, '0')) continue; // якщо злетіли оптові ціни, і рівні "0".
            $this->updated = false;
            $this->result = '';
            $sizes = $this->getAllSizes($url);
            $priceOpt = $this->getOptPrice();
            $available = array();
            if ($sizes) {
                $this->showStep();
                foreach ($sizes as $size) {
                    $sizeQuantity = $this->getQuantity($size);
                    $sizeName = $sizeQuantity[0];
                    $quantity = $sizeQuantity['quantity'];
                    $available[$sizeName] = $quantity;
                    $existSize = $this->connectDb->checkExistSize($id, $sizeName);
                    if ($existSize) {
                        if ($existSize['quantity'] == $quantity) {
                            $this->showAddUpdateQuantity($sizeName, self::MSG_SIZE_ACTUAL);
                        } else {
                            $result = $this->connectDb->updateQuantity($id, $sizeName, $quantity);
                            $this->showAddUpdateQuantity($sizeName, $result);
                        }
                    } else {
                        $result = $this->connectDb->addQuantity($id, $sizeName, $quantity);
                        $this->showAddUpdateQuantity($sizeName, $result);
                    }
                }
                $this->updateSizes($id, $available);
                if ($this->login) {
                    if (!$this->connectDb->checkActualOptPrices($id, $priceOpt) && $priceOpt != 0) {
                        $result = $this->connectDb->updateOptPrice($id, $priceOpt);
                        if ($result) {
                            $this->updated = true;
                            $this->showUpdateSize("Цена = ".$priceOpt.' - обновилась!');
                        } else {
                            $this->showUpdateSize("Цена = ".$priceOpt.' - не обновилась!');
                        }
                    } else {
                        $this->showUpdateSize("Цена = ".$priceOpt.' - актуальна.');
                    }
                }
            } else {
                if ($this->findSaled()) {
                    $available = null;
                    $result = $this->connectDb->hideItem($id);
                    $this->showHide($result);
                    $this->updated = true;
                    $this->hide++;
                } else {
                    echo "<h1>Error, cant find sizes on Cardo HTML page!</h1>";
                    continue;
                }
            }
            $this->allItems[$id] = $available;
            $this->showInfo($id, $url);
            $this->step = $this->getStep($url);
            $this->connectDb->setInterface($this->count, $this->step, $this->updated, $this->result);
//            if ($id > 1419) break;
        }
        $this->end = new \DateTime('now');
        return $this->allItems;
    }

    public function updateSizes($id, $available)
    {
//        var_dump($available);
        $sizes = '';
        foreach ($available as $size => $quantity) {
            if ($quantity > 0) {
                $sizes .= $size . ';';
            }
        }
        echo "Размери = " . $sizes;
        if (strlen($sizes) > 0) {
            $sizes = substr($sizes, 0, strlen($sizes) - 1);
            if ($this->connectDb->checkActualSizes($id, $sizes)) {
                $this->showActualSizes();
            } else {
                $result = $this->connectDb->updateSize($id, $sizes);
                $this->updated = true;
                $this->showUpdateSize($result);
            }
        } else {
              echo "РАЗМЕР - ПУСТАЯ СТРОКА!";
//            $result = $this->connectDb->hideItem($id);
//            $this->showHide($result);
        }
    }

    private function findSaled()
    {
        $webProdano = $this->driver->findElements(By::xPath('//span[@class="prodano"]'));
        if (count($webProdano) > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getStep($url)
    {
        $stepId = array_search($url, $this->urls);
        $stepKeys = array_keys($this->urls);
        return array_search($stepId, $stepKeys) + 1;
    }

    public function showHide($hide)
    {
        echo "<font color = 'red'>" . $hide . "</font>";
        $this->result .= "<font color = 'red'>" . $hide . "</font>";
    }

    public function showUpdateSize($updateSize)
    {
        echo $updateSize . "<br>";
        $this->result .= $updateSize . "<br>";
    }

    private function showStep()
    {
        echo "<font color = 'blue'>STEP - " . (count($this->allItems) + 1) . "</font><br>";
    }

    private function showAddUpdateQuantity($sizeName, $result)
    {
        echo $sizeName . " - " . $result . "<br>";
        $this->result .= $sizeName . " - " . $result . "<br>";
    }

    private function showInfo($id, $url)
    {
        echo "ID = " . $id . "<br>URL = <a href='" . $url . "' target = '_blank'>" . $url . "</a><hr>";
        $this->result .= "ID = " . $id . "<br>URL = <a href='" . $url . "' target = '_blank'>" . $url . "</a><hr>";
    }

    private function showActualSizes()
    {
        echo " - Размери актуальн!<br>";
        $this->result .= " - Размери актуальн!<br>";
    }

    public function showDuration()
    {
        $count = $this->step;
        $hide = $this->hide;
        $seconds = $this->end->diff($this->begin)->s;
        $minutes = $this->end->diff($this->begin)->i;
        $hours = $this->end->diff($this->begin)->h;
        $begin = $this->begin->format('d-m-Y H:i:s');
        $end = $this->end->format('d-m-Y H:i:s');
        $updated = $this->connectDb->getUpdated();
        $this->result = <<<FFFFFF
Всeго товарoв прoeeрено - $count<br>
Обновлено - $updated<br>
В том числе скрито - $hide <br>
Начало парсинга - $begin<br>
Окончание парсинга - $end<br>
Длительность парсинга - $hours:$minutes:$seconds.

FFFFFF;
        $this->connectDb->setInterfaceComplete($this->result, $end);
        echo $this->result;
    }
}
