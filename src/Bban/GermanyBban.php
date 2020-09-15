<?php

namespace IbanGenerator\Bban;

use InvalidArgumentException;

class GermanyBban implements BbanInterface
{
    /**
     * @var string
     */
    private $bankCode;

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * GermanyBban constructor.
     *
     * @param string $bankCode
     * @param string $accountNumber
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $bankCode,
        $accountNumber
    ) {
        self::validateBankCodeFormat($bankCode);
        self::validateAccountNumberFormat($accountNumber);

        $this->bankCode = $bankCode;
        $this->accountNumber = $accountNumber;
    }

    /**
     * @param string $bban
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public static function fromString($bban)
    {
        $bban = preg_replace('/[^0-9a-zA-Z]+/', '', $bban);

        if (! preg_match('/^[\d]{18}$/', $bban)) {
            throw new InvalidArgumentException('Bban should be 18 numbers');
        }

        $bankCode = substr($bban, 0, 8);
        $accountNumber = substr($bban, 8, 10);

        return new static($bankCode, $accountNumber);
    }

    /**
     * @return string
     */
    public function bankCode()
    {
        return $this->bankCode;
    }

    /**
     * @return string
     */
    public function branchCode()
    {
        return '';
    }

    /**
     * @return string
     */
    public function checkDigits()
    {
        return '';
    }

    /**
     * @return string
     */
    public function accountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->bankCode . $this->accountNumber;
    }

    /**
     * @param $bankCode
     *
     * @throws InvalidArgumentException
     */
    private static function validateBankCodeFormat($bankCode)
    {
        if (! preg_match('/^[\d]{8}$/', $bankCode)) {
            throw new InvalidArgumentException('Bank code should be 8 numbers');
        }
    }

    /**
     * @param $accountNumber
     *
     * @throws InvalidArgumentException
     */
    private static function validateAccountNumberFormat($accountNumber)
    {
        if (! preg_match('/^[\d]{10}$/', $accountNumber)) {
            throw new InvalidArgumentException('Account number should be 10 numbers');
        }
    }
}
