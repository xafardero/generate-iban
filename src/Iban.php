<?php

namespace IbanGenerator;

/**
 * Class Iban
 *
 * Description
 *
 * @link
 */
class Iban implements IbanInterface
{
    /**
     * Generate an Iban code
     *
     * @param string $paisCode
     * @param string $ccc
     *
     * @return string
     */
    public function generate($countryCode, $ccc)
    {

        $number = $ccc 
                . $this->code(substr($countryCode, 0, 1))
                . $this->code(substr($countryCode, 1, 1))
                . '00';

        $checksum =  98 - bcmod($number, '97');

        if (strlen($checksum) === 1) {
            $checksum = '0' . $checksum;
        }

        return $countryCode . $checksum;
    }

    private function code($letter)
    {
        $tabla = [
            'A' => '10',
            'B' => '11',
            'C' => '12',
            'D' => '13',
            'E' => '14',
            'F' => '15',
            'G' => '16',
            'H' => '17',
            'I' => '18',
            'J' => '19',
            'K' => '20',
            'L' => '21',
            'M' => '22',
            'N' => '23',
            'O' => '24',
            'P' => '25',
            'Q' => '26',
            'R' => '27',
            'S' => '28',
            'T' => '29',
            'U' => '30',
            'V' => '31',
            'W' => '32',
            'X' => '33',
            'Y' => '34',
            'Z' => '35' 
        ];

        return $tabla[$letter];
    }
}
