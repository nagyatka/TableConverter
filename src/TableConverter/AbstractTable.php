<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 20.
 * Time: 14:54
 */

namespace TableConverter;


class AbstractTable
{
    /**
     * @var array
     */
    private $header;

    /**
     * @var array
     */
    private $rows;

    /**
     * @var int
     */
    private $counter;

    /**
     * AbstractTable constructor.
     * @param array $header
     * @param array $rows
     */
    public function __construct(array $header, array $rows)
    {
        $this->header = $header;
        $this->rows = $rows;
        $this->counter = 0;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return bool|mixed
     */
    public function nextRow() {
        return $this->counter < count($this->rows) ? $this->rows[$this->counter++] : false;
    }

    /**
     *
     */
    public function resetCounter() {
        $this->counter = 0;
    }

    public function getAllRow() {
        return $this->rows;
    }

}