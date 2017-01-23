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
        $abstractTable = $decoder->getAbstractTable();
        $associationRule->setOriginalHeader($abstractTable->getHeader());
        return $coder->getCodedTable($decoder->getAbstractTable(),$associationRule);
    }
}