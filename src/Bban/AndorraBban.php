<?php

namespace IbanGenerator\Bban;

use InvalidArgumentException;

class AndorraBban implements BbanInterface
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
    private $accountNumber;

    /**
     * AndorraBban constructor.
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $accountNumber
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        $bankCode,
        $branchCode,
        $accountNumber
    ) {
        self::validateBankCodeFormat($bankCode);
        self::validateBranchCodeFormat($branchCode);
        self::validateAccountNumberFormat($accountNumber);

        $this->bankCode = $bankCode;
        $this->branchCode = $branchCode;
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

        if (! preg_match('/^[\d]{20}$/', $bban)) {
            throw new InvalidArgumentException('Bban should be 20 numbers');
        }

        $bankCode = substr($bban, 0, 4);
        $branchCode = substr($bban, 4, 4);
        $accountNumber = substr($bban, 8, 12);

        return new static($bankCode, $branchCode, $accountNumber);
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
        return $this->bankCode . $this->branchCode . $this->accountNumber;
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
     * @param $accountNumber
     *
     * @throws InvalidArgumentException
     */
    private static function validateAccountNumberFormat($accountNumber)
    {
        if (! preg_match('/^[\d]{12}$/', $accountNumber)) {
            throw new InvalidArgumentException('Account number should be 10 numbers');
        }
    }
}
