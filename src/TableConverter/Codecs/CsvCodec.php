<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 23.
 * Time: 14:55
 */

namespace TableConverter\Codecs;


use PHPExcel;
use PHPExcel_IOFactory;
use TableConverter\AbstractTable;
use TableConverter\AssociationRule;

class CsvCodec implements Coder, Decoder
{
    /**
     * @var string
     */
    private $filename;

    /**
     * CsvCodec constructor.
     * @param $filename
     */
    public function __construct($filename = null)
    {
        $this->filename = $filename;
    }


    public function getCodedTable(AbstractTable $abstractTable, AssociationRule $associationRule)
    {
        $associationRule->setOriginalHeader($abstractTable->getHeader());
        $newTable = $associationRule->applyRulesOnAbstractTable($abstractTable);
        $xlsArray = $newTable->getAllRow();
        array_unshift($xlsArray,$newTable->getHeader());
        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0);
        $doc->getActiveSheet()->fromArray($xlsArray, null, 'A1');
        $writer = PHPExcel_IOFactory::createWriter($doc, 'Excel5');
        if($this->filename == null) {
            ob_start();
            $writer->save('php://output');
            $excelOutput = ob_get_clean();
            return $excelOutput;
        } else {
            $writer->save($this->filename);
            return true;
        }
    }

    public function getAbstractTable()
    {
        return (new XlsCodec($this->filename))->getAbstractTable();
    }
}