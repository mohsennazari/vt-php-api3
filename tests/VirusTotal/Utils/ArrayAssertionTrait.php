<?php

namespace Tests\Monaz\VirusTotal\Utils;

trait ArrayAssertionTrait
{
    public function assertArrayStructure(array $structure, array $arrayData)
    {
        foreach ($structure as $key => $value) {
            if (is_array($value) && $key === '*') {
                $this->assertInternalType('array', $arrayData);

                foreach ($arrayData as $arrayDataItem) {
                    $this->assertArrayStructure($structure['*'], $arrayDataItem);
                }
            } elseif (is_array($value)) {
                $this->assertArrayHasKey($key, $arrayData);

                $this->assertArrayStructure($structure[$key], $arrayData[$key]);
            } else {
                $this->assertArrayHasKey($value, $arrayData);
            }
        }

        return $this;
    }
}
