<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 20.
 * Time: 14:53
 */

namespace TableConverter;

/**
 * Class AssociationRule
 *
 * The AssociationRule class determines the association rules between the original and the new table.
 *
 * In the constructor you can set a new header (if you want use a totally different header or there are some difference
 * between the original and the new one). In default case, it supposes that the two table has same header, thus it will
 * use the original one, you can use a parameterless constructor.
 * In some cases there are differences between the original and the new one, so you can use
 *
 * @package TableConverter
 */
abstract class AssociationRule
{
    /**
     * @var array
     */
    private $originalHeader;

    /**
     * @var array
     */
    private $newHeader;

    /**
     * @var array
     */
    private $erasedColumns;

    /**
     * [
     *  "from1" => "to1",
     *  "from2" => [
     *      "to" => "column_name",
     *      "value" => value
     *  ]
     *  ....
     * ]
     *
     * @var array
     */
    private $rules;

    /**
     *
     *
     * @var array
     */
    private $newFields;

    /**
     * @var bool
     */
    private $mergeWithOriginalHeader;

    /**
     * @var bool
     */
    private $preventBasicRules;

    /**
     * AssociationRule constructor.
     * @param array $newHeader List of the new columns in the new table.
     * @param array $erasedColumns These columns will be erased from new table.
     * @param bool $mergeWithOriginalHeader
     * @param bool $preventBasicRules
     */
    public function __construct(array $newHeader = [], array $erasedColumns = [], $mergeWithOriginalHeader = true, $preventBasicRules = false)
    {
        $this->newHeader = $newHeader;
        $this->mergeWithOriginalHeader = $mergeWithOriginalHeader;
        $this->preventBasicRules = $preventBasicRules;
        $this->erasedColumns = $erasedColumns;
    }

    /**
     * @param array $rules
     * @param array $originalHeader
     * @param array $newHeader
     * @return array
     */
    abstract function extendRules(array $rules, array $originalHeader, array $newHeader);

    /**
     * @param array $newFields
     * @return mixed
     */
    abstract function extendNewFields(array $newFields);

    /**
     * @param array $originalHeader
     */
    public function setOriginalHeader(array $originalHeader) {
        $this->originalHeader = $originalHeader;
        $this->newHeader = array_diff(
            ($this->mergeWithOriginalHeader ? array_unique(array_merge($this->originalHeader,$this->newHeader)) : $this->newHeader),
            $this->erasedColumns
        );
        $this->rules = $this->preventBasicRules ? $this->extendRules([],$this->originalHeader,$this->newHeader) : $this->extendRules($this->loadBasicRules(),$this->originalHeader,$this->newHeader);
        $this->newFields = $this->extendNewFields($this->loadNewFields());
    }

    /**
     * @return array
     */
    private function loadBasicRules() {
        $rules = [];
        foreach ($this->originalHeader as $item) {
            if(in_array($item,$this->newHeader)) {
                $rules[$item] = $item;
            }
        }
        return [];
    }

    /**
     * @return array
     */
    private function loadNewFields() {
        $newFields = [];
        $diff = array_diff($this->originalHeader,$this->newHeader);
        foreach ($diff as $item) {
            $newFields[$item] = null;
        }
        return $newFields;
    }

    /**
     * @param AbstractTable $abstractTable
     * @return AbstractTable
     */
    public function applyRulesOnAbstractTable(AbstractTable $abstractTable) {
        $abstractTableHeader = $this->newHeader;
        $tableRows = [];
        while (($row = $abstractTable->nextRow()) != false ) {
            $tempRow = [];
            foreach ($this->rules as $from => $to) {
                if(is_array($to)) {
                    $tempRow[$to["to"]] = is_callable($to["value"]) ? $to["value"]($row) : $to["value"];
                } else {
                    $tempRow[$to] = $row[$from];
                }
            }
            foreach ($this->newFields as $name => $value) {
                $tempRow[$name] = is_callable($value) ? $value($row): $value;
            }
            $tableRows[] = $tempRow;
        }
        return new AbstractTable($abstractTableHeader,$tableRows);
    }
}