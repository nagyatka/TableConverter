<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 24.
 * Time: 11:12
 */

namespace TableConverter\Codecs;


abstract class FileCodec implements Coder ,Decoder
{

    /**
     * @var string
     */
    private $filename;

    /**
     * FileCodec constructor.
     * @param null $filename
     * @throws \Exception
     */
    public function __construct($filename = null)
    {
        $this->filename = $filename;
        if($this->filename!= null && !is_file($this->filename)) {
            throw new CodecException("Codec error: File does not exist: ".$this->filename);
        }
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

}