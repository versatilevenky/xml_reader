<?php

namespace App\Helper;

use App\Exceptions\XMLLoadException;

class GetXMLData
{
    private array $xmlData = [];
    private mixed $xmlObject;

    public function __construct($xmlObject)
    {
        $this->xmlObject = $xmlObject;
        $this->parseData();
    }

    /**
     * @return array
     */
    public function getXmlData(): array
    {
        if (count($this->xmlData) == 0) {
            throw new XMLLoadException('No Data in XML');
        }
        return $this->xmlData;
    }

    public function parseData()
    {
        foreach ($this->xmlObject->children() as $child) {
            $datum = [];
            foreach ($child as $element) {
                $key = $element->getName();
                $datum[$key] = $element;
            }
            $this->xmlData[] = $datum;
        }
    }
}
