<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 21.
 * Time: 13:44
 */

namespace TableConverter\Codecs;


use TableConverter\AbstractTable;

class MysqliCodec implements Coder, Decoder
{

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var \mysqli
     */
    private $connection;

    /**
     * @var string
     */
    private $sql;

    /**
     * MysqliCodec constructor.
     *
     * @param string $tableName
     * @param \mysqli $connection
     * @param string $sql
     */
    public function __construct(\mysqli $connection, $tableName, $sql = null)
    {
        $this->tableName            = $tableName;
        $this->connection           = $connection;
        $this->sql                  = $sql;
    }

    /**
     * It inserts the rows in the specified table which you set in constructor. If the insertions were successful the
     * return value is true, otherwise it will throw CodecException.
     *
     * @param AbstractTable $abstractTable
     * @return bool
     * @throws CodecException
     */
    public function getCodedTable(AbstractTable $abstractTable)
    {
        try {
            $this->connection->autocommit(false);

            $header = $abstractTable->getHeader();
            $insertQuery = "INSERT INTO ".$this->tableName." (`".implode("`,`",$header)."`) VALUES (";
            while (($row = $abstractTable->nextRow()) != false) {
                $temp = [];
                foreach ($header as $item) {
                    if(is_string($row[$item])) {
                        $str = $this->connection->real_escape_string($row[$item]);
                        $temp[] = "''".$str."'";
                    } else {
                        $temp[] = $row[$item];
                    }
                }
                if($this->connection->query($insertQuery.implode(",",$header).")") == false) {
                    throw new \Exception("An error occurred during insert (MySQLi Codec): ".$this->connection->error);
                }
            }
            $this->connection->commit();
            return true;
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw new CodecException($e->getMessage());
        } finally {
            $this->connection->autocommit(true);
        }
    }

    /**
     * Returns with AbstractTable
     *
     * @return AbstractTable
     * @throws \Exception
     */
    public function getAbstractTable()
    {
        $dataQuery = $this->sql != null ? $this->sql : "SELECT * FROM ".$this->tableName;
        if (($result = $this->connection->query($dataQuery)) == false ) {
            throw new CodecException('An error occurred during rows selection (MySQLi Codec):' . $this->connection->error . ']');
        }

        $rows = [];
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $rows[] =  $row;
            }
        }
        $result->close();

        return (new ArrayCodec($rows))->getAbstractTable();
    }
}