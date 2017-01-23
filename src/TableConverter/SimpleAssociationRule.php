<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 20.
 * Time: 15:41
 */

namespace TableConverter;


class SimpleAssociationRule extends AssociationRule
{
    /**
     * @param array $rules
     * @param array $originalHeader
     * @param array $newHeader
     * @return array
     */
    function extendRules(array $rules, array $originalHeader, array $newHeader)
    {
        return $rules;
    }

    /**
     * @param array $newFields
     * @return array
     */
    function extendNewFields(array $newFields)
    {
        return $newFields;
    }
}