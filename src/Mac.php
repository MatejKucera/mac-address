<?php
namespace MatejKucera\MacAddress;

use InvalidArgumentException;

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
}