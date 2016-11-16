<?php

namespace IbanGenerator;

/**
 * Class Iban
 *
 * Description
 *
 * @link
 */
interface IbanInterface
{
    /**
     * Generate an Iban code
     *
     * @param string $paisCode
     * @param string $ccc
     *
     * @return string
     */
    public function generate($countryCode, $ccc);
}
