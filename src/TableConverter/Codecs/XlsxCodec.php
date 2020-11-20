<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 24.
 * Time: 11:02
 */

namespace TableConverter\Codecs;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use TableConverter\AbstractTable;

class XlsxCodec implements Coder ,Decoder
{

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $sheet_name;
    /**
     * @var array
     */
    private $styles;
    /**
     * @var array
     */
    private $formats;

    /**
     * XlsCodec constructor.
     *
     * @param string $filename
     * @param string $sheet_name
     * @param array $styles
     * @param array $formats
     */
    public function __construct($filename = null, $sheet_name = '', $styles = [], $formats=[])
    {
        $this->filename = $filename;
        $this->sheet_name = $sheet_name;
        $this->styles = $styles;
        $this->formats = $formats;
    }

    /**
     * @param AbstractTable $abstractTable
     * @return mixed
     * @throws Exception
     */
    public function getCodedTable(AbstractTable $abstractTable)
    {
        $xlsArray = $abstractTable->getAllRow();
        array_unshift($xlsArray,$abstractTable->getHeader());

        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle($this->sheet_name == '' ? 'Sheet 1' : $this->sheet_name);

        $worksheet->fromArray($xlsArray);

        foreach ($this->styles as $selectedCells => $styleFormatArray) {
            $worksheet->getStyle($selectedCells)->applyFromArray($styleFormatArray);
        }

        foreach ($this->formats as $selectedCells => $formatCode) {
            $worksheet->getStyle($selectedCells)->getNumberFormat()->setFormatCode($formatCode);
        }



        $writer = IOFactory::createWriter($spreadsheet, "Xlsx");
        if($this->filename == null) {
            ob_start();
            $writer->save('php://output');
            $output = ob_get_clean();
            return $output;
        }
        else {
            $writer->save($this->filename);
            return true;
        }
    }

    /**
     * @return AbstractTable
     */
    public function getAbstractTable()
    {
        // TODO: not updated
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