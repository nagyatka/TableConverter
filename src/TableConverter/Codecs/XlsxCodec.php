<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 24.
 * Time: 11:02
 */

namespace TableConverter\Codecs;

use PHPExcel_IOFactory;
use TableConverter\AbstractTable;
use XLSXWriter;

class XlsxCodec implements Coder ,Decoder
{

    /**
     * @var string
     */
    private $filename;

    /**
     * XlsCodec constructor.
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        $this->filename = $filename;
    }

    /**
     * @param AbstractTable $abstractTable
     * @return mixed
     */
    public function getCodedTable(AbstractTable $abstractTable)
    {
        $xlsArray = $abstractTable->getAllRow();
        array_unshift($xlsArray,$abstractTable->getHeader());

        $writer = new XLSXWriter();
        $writer->writeSheet($xlsArray);
        if($this->filename == null) {
            return $writer->writeToString();
        } else {
            $writer->writeToFile($this->filename);
            return true;
        }

        /*
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
        */
    }

    /**
     * @return AbstractTable
     */
    public function getAbstractTable()
    {
        $objPHPExcel    = PHPExcel_IOFactory::load($this->filename);
        $objWorksheet   = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow     = $objWorksheet->getHighestRow();
        $highestColumn  = $objWorksheet->getHighestColumn();

        $headingsArray  = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
        $headingsArray  = $headingsArray[1];
        $r = -1;
        $namedDataArray = array();
        for ($row = 2; $row <= $highestRow; ++$row) {
            $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
            if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                ++$r;
                foreach($headingsArray as $columnKey => $columnHeading) {
                    $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                }
            }
        }

        return (new ArrayCodec($namedDataArray))->getAbstractTable();
    }
}