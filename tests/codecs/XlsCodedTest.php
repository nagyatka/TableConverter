<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 23.
 * Time: 12:17
 */

namespace codecs;

use TableConverter\Codecs\ArrayCodec;
use TableConverter\Codecs\XlsCodec;
use TableConverter\Converter;
use TableConverter\SimpleAssociationRule;

/**
 * @covers XlsCodec
 */
class XlsCodedTest extends \PHPUnit_Framework_TestCase
{
    public function testInitializeWithFilename() {
        $xlsCodec = new XlsCodec("tests/test_files/fruits.xls");
        $this->assertInstanceOf(XlsCodec::class,$xlsCodec);
    }

    public function testLoadHeaderFromXls() {
        $xlsCodec = new XlsCodec("tests/test_files/import_teszt_excel.xlsx");
        $header = $xlsCodec->getAbstractTable()->getHeader();
        $this->assertEquals(["Mezo_1","Mezo_2","Mezo_3"],["Mezo_1","Mezo_2","Mezo_3"]);
    }

    public function testXlsxConvert() {
        $testArray = [
            [
                "asd" => 1,
                "qwe" => 2,
            ],
            [
                "asd" => 3,
                "qwe" => 4,
            ]
        ];

        Converter::convert(new ArrayCodec($testArray), new SimpleAssociationRule(), new XlsCodec("testXlsxConvert.xlsx"));
        $result = Converter::convert( new XlsCodec("testXlsxConvert.xlsx"), new SimpleAssociationRule(),new ArrayCodec());
        $this->assertEquals($testArray,$result);
    }

}
