<?php

use MatejKucera\MacAddress\Mac;
use PHPUnit\Framework\TestCase;

final class BasicTest extends TestCase
{

    public function testDefaults(): void {
        $mac = Mac::parse("FFFFFFFFFFFF");
        $this->assertEquals('FF:FF:FF:FF:FF:FF', $mac->get());

        $mac = Mac::parse("1234567890AB");
        $this->assertEquals('12:34:56:78:90:AB', $mac->get());
    }

    public function testCase() :void {
        # default uppercasing
        $mac = Mac::parse("ffffffaaaaaa");
        $this->assertEquals('FF:FF:FF:AA:AA:AA', $mac->get());

        # setting the case
        $mac = Mac::parse("ffffffffffff");
        $mac->setCase(Mac::CASE_LOWER);
        $this->assertEquals('ff:ff:ff:ff:ff:ff', $mac->get());
        $mac->setCase(Mac::CASE_UPPER);
        $this->assertEquals('FF:FF:FF:FF:FF:FF', $mac->get());

        # passing as argument
        $mac = Mac::parse("eeeeeeeeeeee");
        $this->assertEquals('eeeeeeeeeeee', $mac->get(Mac::FORMAT_PLAIN, Mac::CASE_LOWER));
        $this->assertEquals('EEEEEEEEEEEE', $mac->get(Mac::FORMAT_PLAIN, Mac::CASE_UPPER));
    }

    public function testFormat() {
        $mac = Mac::parse("1234567890AB");
        $this->assertEquals('1234567890AB',      $mac->get(Mac::FORMAT_PLAIN));
        $this->assertEquals('12:34:56:78:90:AB', $mac->get(Mac::FORMAT_COLON_PER_TWO));
        $this->assertEquals('1234:5678:90AB',    $mac->get(Mac::FORMAT_COLON_PER_FOUR));
        $this->assertEquals('12.34.56.78.90.AB', $mac->get(Mac::FORMAT_DOT_PER_TWO));
        $this->assertEquals('1234.5678.90AB',    $mac->get(Mac::FORMAT_DOT_PER_FOUR));
        $this->assertEquals('12-34-56-78-90-AB', $mac->get(Mac::FORMAT_DASH_PER_TWO));
        $this->assertEquals('12_34_56_78_90_AB', $mac->get('xx_xx_xx_xx_xx_xx'));

        $mac->setFormat(Mac::FORMAT_PLAIN);
        $this->assertEquals('1234567890AB',      $mac->get());
    }

    public function testGlobals() {
        Mac::setGlobalCase(Mac::CASE_LOWER);
        Mac::setGlobalFormat(Mac::FORMAT_PLAIN);
        $mac = Mac::parse("1234567890AB");
        $this->assertEquals("1234567890ab", $mac->get());
        $mac = Mac::parse("FFFFFFFFFFFF");
        $this->assertEquals("ffffffffffff", $mac->get());
        Mac::setGlobalFormat(Mac::FORMAT_DASH_PER_TWO);
        $this->assertEquals("ffffffffffff", $mac->get());

        $this->assertEquals(Mac::CASE_LOWER, Mac::getGlobalCase());
        $this->assertEquals(Mac::FORMAT_DASH_PER_TWO, Mac::getGlobalFormat());
    }

    public function testArgumentException() {
        $this->expectException(InvalidArgumentException::class);
        Mac::parse('aa:bb:cc:dd:ee');
    }

    public function testVendors() {
        $vendor9 = Mac::parse("70B3D5DA8555")->vendor();
        $this->assertEquals('70B3D5DA8', $vendor9->prefix);
        $this->assertEquals('Tagarno AS', $vendor9->company);
        $this->assertEquals('Sandovej 4 Horsens Denmark DK DK-8700', $vendor9->address);

        $vendor7 = Mac::parse("7CBACCE65154")->vendor();
        $this->assertEquals('7CBACCE', $vendor7->prefix);
        $this->assertEquals('ALPHA TECHNOLOGIES, LLC', $vendor7->company);
        $this->assertEquals('3030 GILCHRIST ROAD AKRON OH US 44305', $vendor7->address);

        $vendor6 = Mac::parse("78FE3D651485")->vendor();
        $this->assertEquals('78FE3D', $vendor6->prefix);
        $this->assertEquals('Juniper Networks', $vendor6->company);
        $this->assertEquals('1133 Innovation Way Sunnyvale CA US 94089', $vendor6->address);

        $vendorNull = Mac::parse("AABBCCDDEEFF")->vendor();
        $this->assertEquals(null, $vendorNull);
    }
}
