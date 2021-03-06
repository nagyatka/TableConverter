<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 23.
 * Time: 14:55
 */

namespace TableConverter\Codecs;


use PHPExcel;
use PHPExcel_Reader_CSV;
use TableConverter\AbstractTable;

class CsvCodec extends FileCodec
{
    /**
     * @var string
     */
    private $delimiter;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * CsvCodec constructor.
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     */
    public function __construct($filename = null,$delimiter = ",", $enclosure = '"')
    {
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        parent::__construct($filename);
    }


    /**
     * @param AbstractTable $abstractTable
     * @return bool|string
     */
    public function getCodedTable(AbstractTable $abstractTable)
    {
        //Preprocessing AbstractTable
        $xlsArray = $abstractTable->getAllRow();
        array_unshift($xlsArray,$abstractTable->getHeader());
        $doc = new PHPExcel();
        $doc->setActiveSheetIndex(0);
        $doc->getActiveSheet()->fromArray($xlsArray, null, 'A1');

        //Initialize writer
        $writer = new \PHPExcel_Writer_CSV($doc);
        $writer->setDelimiter($this->delimiter);
        $writer->setEnclosure($this->enclosure);

        //Write out
        if($this->getFilename() == null) {
            ob_start();
            $writer->save('php://output');
            $excelOutput = ob_get_clean();
            return $excelOutput;
        } else {
            $writer->save($this->getFilename());
            return true;
        }
    }

    public function getAbstractTable()
    {
        $objReader = new PHPExcel_Reader_CSV();
        $objReader->setDelimiter(';');
        $objReader->setEnclosure('');
        $objReader->setSheetIndex(0);

        $objPHPExcel    = $objReader->load($this->getFilename());
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