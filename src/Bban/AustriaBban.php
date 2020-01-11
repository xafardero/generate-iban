<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use InvalidArgumentException;

use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

class AustriaBban extends AbstractBban
{
    /** @throws InvalidArgumentException */
    public function __construct(
        string $bankCode,
        string $accountNumber
    ) {
        self::validateBankCodeFormat($bankCode);
        self::validateAccountNumberFormat($accountNumber);

        $this->bankCode = $bankCode;
        $this->accountNumber = $accountNumber;
    }

    /** @throws InvalidArgumentException */
    public static function fromString(string $bban): BbanInterface
    {
        $bban = preg_replace('/[^0-9a-zA-Z]+/', '', $bban);

        if (strlen($bban) !== 16) {
            throw new InvalidArgumentException('Bban should be 16 chars long');
        }

        $bankCode = substr($bban, 0, 5);
        $accountNumber = substr($bban, 5, 11);

        return new static($bankCode, $accountNumber);
    }

    public function __toString(): string
    {
        return $this->bankCode . $this->accountNumber;
    }

    /** @throws InvalidArgumentException */
    private static function validateBankCodeFormat(string $bankCode): void
    {
        if (!preg_match('/^[\d]{5}$/', $bankCode)) {
            throw new InvalidArgumentException('Bank code should be 5 numeric characters');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateAccountNumberFormat(string $accountNumber): void
    {
        if (!preg_match('/^[\d]{11}$/', $accountNumber)) {
            throw new InvalidArgumentException('Bank code should be 11 numeric characters');
        }
    }
}
