<?php
/**
 * Created by PhpStorm.
 * User: nagyatka
 * Date: 2017. 01. 24.
 * Time: 12:19
 */

namespace TableConverter\Codecs;


use Exception;

class CodecException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct("Codec exception: ".$message, $code, $previous);
    }

}