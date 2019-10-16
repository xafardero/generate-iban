<?php
declare(strict_types=1);

namespace IbanGenerator\Tests\Bban;

use IbanGenerator\Bban\GermanBban;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GermanBbanTest extends TestCase
{
    /**
     * @dataProvider validBbanStrings
     *
     *
     * @param string $bbanString
     */
    public function testCreateFromStringMustHave18Digits($bbanString)
    {
        GermanBban::fromString($bbanString);
    }

    /**
     * @return array
     */
    public function validBbanStrings()
    {
        return [
            ['208520666403000828'],
            ['004923520724142054'],
        ];
    }
}
