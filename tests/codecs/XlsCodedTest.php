<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 23.
 * Time: 12:17
 */

namespace codecs;

use TableConverter\Codecs\XlsCodec;

/**
 * @covers XlsCodec
 */
class XlsCodedTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInitializedWithFilename() {
        $xlsCodec = new XlsCodec("tests/test_files/fruits.xls");
        $this->assertInstanceOf(XlsCodec::class,$xlsCodec);
    }

    public function testLoadHeaderFromXls() {
        $xlsCodec = new XlsCodec("tests/test_files/import_teszt_excel.xlsx");
        $header = $xlsCodec->getAbstractTable()->getHeader();
        $this->assertEquals(["Mezo_1","Mezo_2","Mezo_3"],["Mezo_1","Mezo_2","Mezo_3"]);
    }


}
