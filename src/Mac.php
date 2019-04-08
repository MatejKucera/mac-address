<?php
namespace MatejKucera\MacAddress;

use InvalidArgumentException;
use PDO;

class Mac
{

    public const CASE_LOWER = 1;
    public const CASE_UPPER = 2;

    public const FORMAT_DOT_PER_TWO = 'xx.xx.xx.xx.xx.xx';
    public const FORMAT_DOT_PER_FOUR= 'xxxx.xxxx.xxxx';
    public const FORMAT_COLON_PER_TWO = 'xx:xx:xx:xx:xx:xx';
    public const FORMAT_COLON_PER_FOUR = 'xxxx:xxxx:xxxx';
    public const FORMAT_DASH_PER_TWO = 'xx-xx-xx-xx-xx-xx';
    public const FORMAT_PLAIN = 'xxxxxxxxxxxx';

    private $_mac;
    private $_case = self::CASE_UPPER;
    private $_format = self::FORMAT_COLON_PER_TWO;

    private static $_globalCase = null;
    private static $_globalFormat = null;

    private static $_db = null;

    public function __construct($mac)
    {
        if($validMac = $this->_validFormat($mac)) {
            $this->_mac = $validMac;
        }

        if(self::$_globalCase) {
            $this->_case = self::$_globalCase;
        }

        if(self::$_globalFormat) {
            $this->_format = self::$_globalFormat;
        }
    }

    public static function parse(string $mac) {
        return new Mac($mac);
    }

    private function _validFormat(string $mac) {
        $mac = preg_replace('/[^A-F0-9]/', '', strtoupper($mac));
        if(preg_match('/[A-F0-9]{12}/', $mac)) {
            return $mac;
        } else {
            throw new InvalidArgumentException("Invalid MAC address format.");
        }
    }

    public function setCase(int $case) {
        $this->_case = $case;
    }

    public function setFormat(string $format) {
        $this->_format = $format;
    }

    public function get(string $format = null, $case = null) {
        $usedFormat = $format ? $format : $this->_format;
        $usedCase   = $case   ? $case   : $this->_case;

        return $this->_getFormattedMac($usedFormat, $usedCase);
    }


    private function _getFormattedMac(string $format, int $case) :string {

        $mac = $this->_processFormat($format);

        if($case == self::CASE_LOWER) {
            $mac = strtolower($mac);
        }

        return $mac;
    }

    private function _processFormat($format) {
        $result = $format;
        foreach(str_split($this->_mac) as $char) {
            $pos = strpos($result, 'x');
            if ($pos !== false) {
                $result = substr_replace($result, $char, $pos, strlen('x'));
            }
        }
        return $result;
    }

    public function vendor() {
        $mac6 = substr($this->get(self::FORMAT_PLAIN),0, 6);
        $mac7 = substr($this->get(self::FORMAT_PLAIN),0, 7);
        $mac9 = substr($this->get(self::FORMAT_PLAIN),0, 9);

        $result9 = self::db()->query('SELECT prefix,company,address FROM vendors WHERE prefix LIKE "'.$mac9.'%"')->fetch();
        if($result9) {
            return new Vendor($result9[0], $result9[1], $result9[2]);
        }

        $result7 = self::db()->query('SELECT prefix,company,address FROM vendors WHERE prefix LIKE "'.$mac7.'%"')->fetch();
        if($result7) {
            return new Vendor($result7[0], $result7[1], $result7[2]);
        }

        $result6 = self::db()->query('SELECT prefix,company,address FROM vendors WHERE prefix LIKE "'.$mac6.'%"')->fetch();
        if($result6) {
            return new Vendor($result6[0], $result6[1], $result6[2]);
        }

        return null;
    }

    public static function getGlobalCase() :?int {
        return self::$_globalCase;
    }

    public static function getGlobalFormat() :?string {
        return self::$_globalFormat;
    }

    public static function setGlobalCase(int $case) :void {
        self::$_globalCase = $case;
    }

    public static function setGlobalFormat(string $format) :void {
        self::$_globalFormat = $format;
    }

    private static function db() {
        if(self::$_db == null) {
            self::$_db = new PDO('sqlite:data/vendors.db');
        }

        return self::$_db;
    }
}