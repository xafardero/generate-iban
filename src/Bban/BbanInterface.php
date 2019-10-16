<?php
declare(strict_types=1);
namespace IbanGenerator\Bban;

use InvalidArgumentException;

interface BbanInterface
{
    /**
     * @param string $bban
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public static function fromString($bban);

    /**
     * @return string
     */
    public function bankCode();

    /**
     * @return string
     */
    public function branchCode();

    /**
     * @return string
     */
    public function checkDigits();

    /**
     * @return string
     */
    public function accountNumber();

    /**
     * @return string
     */
    public function __toString();
}
