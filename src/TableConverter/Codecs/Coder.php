<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 20.
 * Time: 14:55
 */

namespace TableConverter\Codecs;


use TableConverter\AbstractTable;
use TableConverter\AssociationRule;

interface Coder
{
    /**
     * @param AbstractTable $abstractTable
     * @param AssociationRule $associationRule
     * @return mixed
     */
    public function getCodedTable(AbstractTable $abstractTable,AssociationRule $associationRule);
}