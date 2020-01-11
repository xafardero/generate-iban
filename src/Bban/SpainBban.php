<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use InvalidArgumentException;

use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

class SpainBban extends AbstractBban
{
    /** @var string */
    private $branchCode;
    /** @var string */
    private $checkDigits;

    /** @throws InvalidArgumentException */
    public function __construct(
        string $bankCode,
        string $branchCode,
        string $checkDigits,
        string $accountNumber
    ) {
        self::validateBankCodeFormat($bankCode);
        self::validateBranchCodeFormat($branchCode);
        self::validateCheckDigitsFormat($checkDigits);
        self::validateAccountNumberFormat($accountNumber);
        self::validateControlDigit(
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

    /** @throws InvalidArgumentException */
    public static function fromString(string $bban): BbanInterface
    {
        $bban = preg_replace('/[^0-9a-zA-Z]+/', '', $bban);

        if (strlen($bban) !== 20) {
            throw new InvalidArgumentException('Bban should be 20 chars long');
        }

        $bankCode = substr($bban, 0, 4);
        $branchCode = substr($bban, 4, 4);
        $checkDigits = substr($bban, 8, 2);
        $accountNumber = substr($bban, 10, 10);

        return new static($bankCode, $branchCode, $checkDigits, $accountNumber);
    }

    public function branchCode(): string
    {
        return $this->branchCode;
    }

    public function checkDigits(): string
    {
        return $this->checkDigits;
    }

    public function __toString(): string
    {
        return $this->bankCode . $this->branchCode . $this->checkDigits . $this->accountNumber;
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
    private static function validateCheckDigitsFormat(string $checkDigits): void
    {
        if (!preg_match('/^[\d]{2}$/', $checkDigits)) {
            throw new InvalidArgumentException('Check digits should be 4 numbers');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateAccountNumberFormat(string $accountNumber): void
    {
        if (!preg_match('/^[\d]{10}$/', $accountNumber)) {
            throw new InvalidArgumentException('Account number should be 10 numbers');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateControlDigit(
        string $bankCode,
        string $branchCode,
        string $checkDigits,
        string $accountNumber
    ): void {
        $dc = '';
        $validations = [6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
        foreach ([$bankCode . $branchCode, $accountNumber] as $string) {
            $suma = 0;
            for ($i = 0, $len = strlen($string); $i < $len; $i++) {
                $suma += $validations[$i] * $string[$len - $i - 1];
            }
            $digit = 11 - $suma % 11;
            if ($digit === 11) {
                $digit = 0;
            } elseif ($digit === 10) {
                $digit = 1;
            }
            $dc .= $digit;
        }
        if ($checkDigits !== $dc) {
            throw new InvalidArgumentException('Control digits are not valid');
        }
    }
}
