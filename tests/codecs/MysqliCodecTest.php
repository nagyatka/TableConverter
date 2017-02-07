<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 02. 07.
 * Time: 15:41
 */

namespace codecs;


use TableConverter\Codecs\MysqliCodec;
use TableConverter\Codecs\XlsxCodec;
use TableConverter\Converter;
use TableConverter\SimpleAssociationRule;

class MysqliCodecTest extends \PHPUnit_Framework_TestCase
{
    public function testWithXlsxImport()
    {
        $con = mysqli_connect("127.0.0.1","root","","smartline_test");
        $con->set_charset("utf8");

        Converter::convert(new XlsxCodec("tests/test_files/import_teszt_excel.xlsx"),new SimpleAssociationRule(), new MysqliCodec($con,"sl_client_input_base"));

        $asd = mysqli_connect_errno();

        $this->assertEquals(0,$asd);
    }
}
