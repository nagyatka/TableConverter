<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 20.
 * Time: 14:55
 */

namespace TableConverter\Codecs;


use TableConverter\AbstractTable;

interface Decoder
{
    /**
     * @return AbstractTable
     */
    public function getAbstractTable();
}