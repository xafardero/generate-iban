<?php

use IbanGenerator\Iban;

/**
 * Ccc validator test.
 *
 * @link 
 *
 * Pattern:
 * EEEE OOOO DC CCCCCCCCCC
 */
class IbanTest extends PHPUnit_Framework_TestCase
{
    public function testCorrectCcc()
    {
        $entidad = '0000';
        $oficina = '0000';
        $cuenta = '0000000000';
        $dc = '00';

        $ibanGenerator = new Iban();

        $code = $ibanGenerator->generate('ES', $entidad . $oficina . $dc  . $cuenta);
        $this->assertEquals($code, 'ES82');
    }
}
