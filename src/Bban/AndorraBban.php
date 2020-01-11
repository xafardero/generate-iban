<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use InvalidArgumentException;

use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

class AndorraBban extends AbstractBban
{
    /** @var string */
    private $branchCode;

    /** @throws InvalidArgumentException */
    public function __construct(
        string $bankCode,
        string $branchCode,
        string $accountNumber
    ) {
        self::validateBankCodeFormat($bankCode);
        self::validateBranchCodeFormat($branchCode);
        self::validateAccountNumberFormat($accountNumber);

        $this->bankCode = $bankCode;
        $this->branchCode = $branchCode;
        $this->accountNumber = $accountNumber;
    }

    /** @throws InvalidArgumentException */
    public static function fromString(string $bban): BbanInterface
    {
        $bban = preg_replace('/[^0-9a-zA-Z]+/', '', $bban);

        if (strlen($bban) !== 20) {
            throw new InvalidArgumentException('Bban should be 20 chars long');
        }

        $bankCode = substr($bban, 0, 4);
        $branchCode = substr($bban, 4, 4);
        $accountNumber = substr($bban, 8, 12);

        return new static($bankCode, $branchCode, $accountNumber);
    }

    public function branchCode(): string
    {
        return $this->branchCode;
    }

    public function __toString(): string
    {
        return $this->bankCode . $this->branchCode . $this->accountNumber;
    }

    /** @throws InvalidArgumentException */
    private static function validateBankCodeFormat(string $bankCode): void
    {
        if (!preg_match('/^[\d]{4}$/', $bankCode)) {
            throw new InvalidArgumentException('Bank code should be 4 numbers');
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
    private static function validateAccountNumberFormat(string $accountNumber): void
    {
        if (!preg_match('/^[\d]{12}$/', $accountNumber)) {
            throw new InvalidArgumentException('Account number should be 10 numbers');
        }
    }
}
