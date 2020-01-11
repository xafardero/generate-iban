<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use InvalidArgumentException;

use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

class BulgariaBban extends AbstractBban
{
    /** @var string */
    private $branchCode;
    /** @var string */
    private $accountType;

    /** @throws InvalidArgumentException */
    public function __construct(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $accountNumber
    ) {
        self::validateBankCodeFormat($bankCode);
        self::validateBranchCodeFormat($branchCode);
        self::validateAccountTypeFormat($accountType);
        self::validateAccountNumberFormat($accountNumber);

        $this->bankCode = $bankCode;
        $this->branchCode = $branchCode;
        $this->accountType = $accountType;
        $this->accountNumber = $accountNumber;
    }

    /** @throws InvalidArgumentException */
    public static function fromString(string $bban): BbanInterface
    {
        $bban = preg_replace('/[^0-9a-zA-Z]+/', '', $bban);

        if (strlen($bban) !== 18) {
            throw new InvalidArgumentException('Bban should be 18 chars long');
        }

        $bankCode = substr($bban, 0, 4);
        $branchCode = substr($bban, 4, 4);
        $accountType = substr($bban, 8, 2);
        $accountNumber = substr($bban, 10, 8);

        return new static($bankCode, $branchCode, $accountType, $accountNumber);
    }

    public function branchCode(): string
    {
        return $this->branchCode;
    }

    public function accountType(): string
    {
        return $this->accountType;
    }

    public function __toString(): string
    {
        return $this->bankCode . $this->branchCode . $this->accountType . $this->accountNumber;
    }

    /** @throws InvalidArgumentException */
    private static function validateBankCodeFormat(string $bankCode): void
    {
        if (!preg_match('/^[A-Z]{4}$/', $bankCode)) {
            throw new InvalidArgumentException('Bank code should be 4 alphabetic characters');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateBranchCodeFormat(string $branchCode): void
    {
        if (!preg_match('/^[\d]{4}$/', $branchCode)) {
            throw new InvalidArgumentException('Branch code should be 4 numbers');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateAccountTypeFormat(string $accountType): void
    {
        if (!preg_match('/^[\d]{2}$/', $accountType)) {
            throw new InvalidArgumentException('Account type should be 2 numbers');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateAccountNumberFormat(string $accountNumber): void
    {
        if (!preg_match('/^[A-Z0-9]{8}$/', $accountNumber)) {
            throw new InvalidArgumentException('Account number should be 8 alphanumerical characters');
        }
    }
}
