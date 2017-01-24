<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 20.
 * Time: 14:52
 */

namespace TableConverter;


use TableConverter\Codecs\Coder;
use TableConverter\Codecs\Decoder;

class Converter
{
    /**
     * @param Decoder $decoder
     * @param AssociationRule $associationRule
     * @param Coder $coder
     * @return mixed
     */
    public static function convert(Decoder $decoder, AssociationRule $associationRule, Coder $coder) {
        //Get AbstractTable from decoder
        $abstractTable = $decoder->getAbstractTable();

        //Apply association rules on AbstractTable
        $associationRule->setOriginalHeader($abstractTable->getHeader());
        $newAbstractTable = $associationRule->applyRulesOnAbstractTable($abstractTable);

        //Return with coded AbstractTable
        return $coder->getCodedTable($newAbstractTable);
    }
}