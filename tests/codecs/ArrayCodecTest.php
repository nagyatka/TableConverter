<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 23.
 * Time: 12:57
 */

namespace codecs;

use TableConverter\Codecs\ArrayCodec;
use TableConverter\SimpleAssociationRule;

/**
 * Class ArrayCodecTest
 * @package codecs
 * @covers ArrayCodec
 */
class ArrayCodecTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInitializedWithEmptyArray() {
        $arrayCodec = new ArrayCodec([]);
        $this->assertInstanceOf(ArrayCodec::class,$arrayCodec);
    }

    public function testLoadHeader() {
        $arrayCodec = new ArrayCodec([
            ["apple" => 3,"peach" => 4,"banana" => 2,"sum" => 9],
            ["apple" => 1,"peach" => 2,"banana" => 2,"sum" => 5],
        ]);
        $this->assertEquals(["apple","peach","banana","sum"],$arrayCodec->getAbstractTable()->getHeader());
    }

    public function testLoadHeaderWithAMissingColumnInARow() {
        $arrayCodec = new ArrayCodec([
            ["apple" => 3,"peach" => 4,"banana" => 2,"sum" => 9],
            ["apple" => 1,"peach" => 2,"banana" => 2],
        ]);
        $this->assertEquals(["apple","peach","banana","sum"],$arrayCodec->getAbstractTable()->getHeader());
    }

    public function testLoadAllRow() {
        $table = [
            ["apple" => 3,"peach" => 4,"banana" => 2,"sum" => 9],
            ["apple" => 1,"peach" => 2,"banana" => 2],
        ];
        $abstractTable = (new ArrayCodec($table))->getAbstractTable();
        $table[1]["sum"] = null;
        $this->assertEquals($table,$abstractTable->getAllRow());
    }

    public function testExportToArray() {
        $arrayCodec = new ArrayCodec();
        $abstractTable = (new ArrayCodec([
            ["apple" => 3,"peach" => 4,"banana" => 2,"sum" => 9],
            ["apple" => 1,"peach" => 2,"banana" => 2],
        ]))->getAbstractTable();
        $codedTable = $arrayCodec->getCodedTable($abstractTable,new SimpleAssociationRule());

        $this->assertEquals([
            ["apple" => 3,"peach" => 4,"banana" => 2,"sum" => 9],
            ["apple" => 1,"peach" => 2,"banana" => 2,"sum" => null],
        ],$codedTable);
    }
}
