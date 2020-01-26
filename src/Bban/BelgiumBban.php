<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use InvalidArgumentException;

use function bcmod;
use function preg_match;
use function preg_replace;
use function str_pad;
use function strlen;
use function substr;

class BelgiumBban extends AbstractBban
{
    /** @var string */
    private $checkDigits;

    /** @throws InvalidArgumentException */
    public function __construct(
        string $bankCode,
        string $accountNumber,
        string $checkDigits
    ) {
        self::validateBankCodeFormat($bankCode);
        self::validateAccountNumberFormat($accountNumber);
        self::validateCheckDigitsFormat($checkDigits);
        self::validateControlDigit(
            $bankCode,
            $accountNumber,
            $checkDigits
        );

        $this->bankCode = $bankCode;
        $this->accountNumber = $accountNumber;
        $this->checkDigits = $checkDigits;
    }

    /** @throws InvalidArgumentException */
    public static function fromString(string $bban): BbanInterface
    {
        $bban = preg_replace('/[^0-9a-zA-Z]+/', '', $bban);

        if (strlen($bban) !== 12) {
            throw new InvalidArgumentException('Bban should be 12 chars long');
        }

        $bankCode = substr($bban, 0, 3);
        $accountNumber = substr($bban, 3, 7);
        $checkDigits = substr($bban, 10, 2);

        return new static($bankCode, $accountNumber, $checkDigits);
    }

    public function checkDigits(): string
    {
        return $this->checkDigits;
    }

    public function __toString(): string
    {
        return $this->bankCode . $this->accountNumber . $this->checkDigits;
    }

    /** @throws InvalidArgumentException */
    private static function validateBankCodeFormat(string $bankCode): void
    {
        if (!preg_match('/^[\d]{3}$/', $bankCode)) {
            throw new InvalidArgumentException('Bank code should be 3 numbers');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateAccountNumberFormat(string $accountNumber): void
    {
        if (!preg_match('/^[\d]{7}$/', $accountNumber)) {
            throw new InvalidArgumentException('Account number should be 7 numbers');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateCheckDigitsFormat(string $checkDigits): void
    {
        if (!preg_match('/^[\d]{2}$/', $checkDigits)) {
            throw new InvalidArgumentException('Check digits should be 2 numbers');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateControlDigit(
        string $bankCode,
        string $accountNumber,
        string $checkDigits
    ): void {
        $stringToCompute = $bankCode . $accountNumber;
        $calculatedChecksum = bcmod($stringToCompute, '97');

        $formattedChecksum = str_pad($calculatedChecksum, 2, '0', STR_PAD_LEFT);

        if ($checkDigits !== $formattedChecksum) {
            throw new InvalidArgumentException('Control digits are not valid');
        }
    }
}
