<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 21.
 * Time: 13:44
 */

namespace TableConverter\Codecs;


use TableConverter\AbstractTable;
use TableConverter\AssociationRule;

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
     * @param string $tableName
     * @param \mysqli $connection
     * @param string $sql
     */
    public function __construct($tableName, \mysqli $connection, $sql = null)
    {
        $this->tableName            = $tableName;
        $this->connection           = $connection;
        $this->sql                  = $sql;
    }

    public function getCodedTable(AbstractTable $abstractTable, AssociationRule $associationRule)
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
                    throw new \Exception("Query error");
                }
            }
            $this->connection->commit();
            return true;
        } catch (\Exception $e) {
            $this->connection->rollback();
            throw new \Exception("MySQLi Codec: There was an error running the query: ".$this->connection->error);
        } finally {
            $this->connection->autocommit(true);
        }
    }

    public function getAbstractTable()
    {
        $schemaQuery = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_NAME`='".$this->tableName."'";
        if(!$result = $this->connection->query($schemaQuery)){
            throw new \Exception('MySQLi Codec: There was an error running the query [' . $this->connection->error . ']');
        }
        $header = [];
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $header[] =  $row['COLUMN_NAME'];
            }
        }
        $result->close();

        $dataQuery = $this->sql != null ? $this->sql : "SELECT * FROM ".$this->tableName;
        if (($result = $this->connection->query($dataQuery)) == false ) {
            throw new \Exception('MySQLi Codec: There was an error running the query [' . $this->connection->error . ']');
        }
        $rows = [];
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $rows[] =  $row;
            }
        }
        $result->close();

        return new AbstractTable($header,$rows);
    }
}