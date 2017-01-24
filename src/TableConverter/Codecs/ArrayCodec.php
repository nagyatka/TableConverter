<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 20.
 * Time: 15:50
 */

namespace TableConverter\Codecs;


use TableConverter\AbstractTable;
use TableConverter\AssociationRule;

class ArrayCodec implements Coder, Decoder
{
    /**
     * @var array
     */
    private $rawArray;

    /**
     * ArrayCodec constructor.
     * @param array $rawArray
     */
    public function __construct(array $rawArray = [])
    {
        $this->rawArray = $rawArray;
    }

    /**
     * Returns with an associative php array.
     *
     * @param AbstractTable $abstractTable
     * @return array
     * @throws CodecException
     */
    public function getCodedTable(AbstractTable $abstractTable)
    {
        $rows = $abstractTable->getAllRow();
        if($rows == null) {
            throw new CodecException("Missing input in ArrayCodec(Coder).");
        }
        return $abstractTable->getAllRow();
    }

    /**
     * Returns with an AbstractTable.
     *
     * @return AbstractTable
     * @throws \Exception
     */
    public function getAbstractTable()
    {
        if(count($this->rawArray) < 1) {
            throw new CodecException("Empty input in ArrayCodec(Decoder).");
        }

        // First we need to check and collect the array keys.
        $header = [];
        $i = 0;
        foreach ($this->rawArray as $row) {
            $i++;
            $header = array_merge($header,array_keys($row));
            if($i % 10 == 0) $header = array_unique($header);
        }
        $header = array_unique($header);


        $rows = [];
        foreach ($this->rawArray as $row) {
            $tempRow = $row;
            $diff = array_diff($header,array_keys($row));
            if(count($diff) > 0) {
                foreach ($diff as $item) {
                    $tempRow[$item] = null;
                }
            }
            $rows[] = $tempRow;
        }

        return new AbstractTable($header,$rows);
    }

}