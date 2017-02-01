<?php

namespace IbanGenerator\Bban;

use InvalidArgumentException;

class SpainBban implements BbanInterface
{
    /**
     * @var string
     */
    private $bankCode;

    /**
     * @var string
     */
    private $branchCode;

    /**
     * @var string
     */
    private $checkDigits;

    /**
     * @var string
     */
    private $accountNumber;

    /**
     * SpainBban constructor.
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $checkDigits
     * @param string $accountNumber
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $bankCode,
        $branchCode,
        $checkDigits,
        $accountNumber
    ) {
        static::validateBankCodeFormat($bankCode);
        static::validateBranchCodeFormat($branchCode);
        static::validateCheckDigitsFormat($checkDigits);
        static::validateAccountNumberFormat($accountNumber);
        static::validateControlDigit(
            $bankCode,
            $branchCode,
            $checkDigits,
            $accountNumber
        );

        $this->bankCode = $bankCode;
        $this->branchCode = $branchCode;
        $this->checkDigits = $checkDigits;
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
        if (! preg_match('/^[\d]{20}$/', $bban)) {
            throw new InvalidArgumentException('Bban should be 20 numbers');
        }

        $bankCode = substr($bban, 0, 4);
        $branchCode = substr($bban, 4, 4);
        $checkDigits = substr($bban, 8, 2);
        $accountNumber = substr($bban, 10, 10);

        return new static($bankCode, $branchCode, $checkDigits, $accountNumber);
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
        return $this->branchCode;
    }

    /**
     * @return string
     */
    public function checkDigits()
    {
        return $this->checkDigits;
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
        return $this->bankCode . $this->branchCode . $this->checkDigits . $this->accountNumber;
    }

    /**
     * @param $bankCode
     *
     * @throws InvalidArgumentException
     */
    private static function validateBankCodeFormat($bankCode)
    {
        if (! preg_match('/^[\d]{4}$/', $bankCode)) {
            throw new InvalidArgumentException('Bank code should be 4 numbers');
        }
    }

    /**
     * @param $branchCode
     *
     * @throws InvalidArgumentException
     */
    private static function validateBranchCodeFormat($branchCode)
    {
        if (! preg_match('/^[\d]{4}$/', $branchCode)) {
            throw new InvalidArgumentException('Branch code should be 4 numbers');
        }
    }

    /**
     * @param $checkDigits
     *
     * @throws InvalidArgumentException
     */
    private static function validateCheckDigitsFormat($checkDigits)
    {
        if (! preg_match('/^[\d]{2}$/', $checkDigits)) {
            throw new InvalidArgumentException('Check digits should be 4 numbers');
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

    /**
     * @param $bankCode
     * @param $branchCode
     * @param $checkDigits
     * @param $accountNumber
     *
     * @throws InvalidArgumentException
     */
    private static function validateControlDigit(
        $bankCode,
        $branchCode,
        $checkDigits,
        $accountNumber
    ) {
        $dc = '';
        $validations = [6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
        foreach ([$bankCode . $branchCode, $accountNumber] as $string) {
            $suma = 0;
            for ($i = 0, $len = strlen($string); $i < $len; $i++) {
                $suma += $validations[$i] * substr($string, $len - $i - 1, 1);
            }
            $digit = 11 - $suma % 11;
            if ($digit == 11) {
                $digit = 0;
            } elseif ($digit == 10) {
                $digit = 1;
            }
            $dc .= $digit;
        }
        if ($checkDigits !== $dc) {
            throw new InvalidArgumentException('Control digits are not valid');
        }
    }
}
