<?php

namespace TableConverter\Codecs;


use TableConverter\AbstractTable;

class PDOCodec implements Coder, Decoder
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
     * PDOCodec constructor.
     *
     * @param string $tableName
     * @param \PDO $connection
     * @param string $sql
     */
    public function __construct(\PDO $connection, $tableName, $sql = null)
    {
        $this->tableName            = $tableName;
        $this->connection           = $connection;
        $this->sql                  = $sql;
    }

    public function getCodedTable(AbstractTable $abstractTable)
    {
        try {
            $this->connection->autocommit(false);

            $header = $abstractTable->getHeader();
            $insertQuery = "INSERT INTO ".$this->tableName." (`".implode("`,`",$header)."`) VALUES (";
            while (($row = $abstractTable->nextRow()) != false) {
                $prepared_statement = $this->connection->prepare($insertQuery);
                foreach ($header as $item) {
                    $prepared_statement->bindValue($item, $row[$item]);
                }

                //Lekérdezés futtatása
                if(!$prepared_statement->execute()) {
                    throw new \Exception("An error occurred during insert (PDO Codec): ");
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

    public function getAbstractTable()
    {
        $dataQuery = $this->sql != null ? $this->sql : "SELECT * FROM ".$this->tableName;

        $stmt = $this->connection->prepare($dataQuery);

        if (!$stmt->execute()) {
            throw new CodecException('An error occurred during rows selection (PDO Codec):' . $this->connection->error . ']');
        }

        return (new ArrayCodec($stmt->fetchAll(\PDO::FETCH_ASSOC)))->getAbstractTable();
    }
}