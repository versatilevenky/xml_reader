<?php

namespace App\Helper;

use App\Exceptions\XMLLoadException;
libxml_use_internal_errors(true);

class XMLParser
{
    private mixed $xmlObject;

    public function __construct($path)
    {
        $this->xmlObject = simplexml_load_file($path);
        if ($this->xmlObject === false) {
            throw new XMLLoadException(libxml_get_last_error()->message);
        }
    }

    public function getXmlObject()
    {
        return $this->xmlObject;
    }
}
