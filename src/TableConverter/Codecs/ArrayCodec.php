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
    public function __construct(array $rawArray = false)
    {
        $this->rawArray = $rawArray;
    }

    /**
     * @param AbstractTable $abstractTable
     * @param AssociationRule $associationRule
     * @return array
     */
    public function getCodedTable(AbstractTable $abstractTable, AssociationRule $associationRule)
    {
        $newTable = $associationRule->applyRulesOnAbstractTable($abstractTable);
        return $newTable->getAllRow();
    }

    /**
     * @return AbstractTable
     * @throws \Exception
     */
    public function getAbstractTable()
    {
        if($this->rawArray == false) {
            throw new \Exception("Missing input in ArrayCodec.");
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