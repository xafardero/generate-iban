<?php

declare(strict_types=1);

namespace IbanGenerator;

use IbanGenerator\Bban;
use InvalidArgumentException;

use function array_key_exists;
use function array_map;
use function bcmod;
use function implode;
use function is_numeric;
use function ord;
use function preg_match;
use function preg_replace;
use function sprintf;
use function str_pad;
use function str_split;
use function strtoupper;
use function substr;

class Iban
{
    /** @var Bban\BbanInterface */
    private $bban;
    /** @var string */
    private $countryCode;
    /** @var string */
    private $checkDigits;
    /** @var array */
    private static $countriesSupported = [
        'ES' => Bban\SpainBban::class,
        'AD' => Bban\AndorraBban::class,
    ];

    /** @throws InvalidArgumentException */
    public function __construct(
        string $countryCode,
        string $checkDigits,
        Bban\BbanInterface $bban
    ) {
        $countryCode = strtoupper($countryCode);
        self::validateCountryCodeFormat($countryCode);
        self::validateCheckDigitsFormat($checkDigits);
        self::validateControlDigit($countryCode, $checkDigits, $bban);
        $this->countryCode = $countryCode;
        $this->checkDigits = $checkDigits;
        $this->bban = $bban;
    }

    /** @throws InvalidArgumentException */
    public static function fromString(string $iban): self
    {
        $iban = preg_replace('/[^0-9a-zA-Z]+/', '', $iban);

        if (!preg_match('/^[0-9a-zA-Z]{16,34}$/', $iban)) {
            throw new InvalidArgumentException('Iban should be between 16 and 34 characters');
        }

        $countryCode = strtoupper(substr($iban, 0, 2));
        $checkDigits = strtoupper(substr($iban, 2, 2));
        $bbanString = strtoupper(substr($iban, 4));

        self::validateSupportedCountry($countryCode);
        $bbanClass = self::$countriesSupported[$countryCode];

        $bban = $bbanClass::fromString($bbanString);

        return new static($countryCode, $checkDigits, $bban);
    }

    /** @throws InvalidArgumentException */
    public static function fromBbanAndCountry(
        Bban\BbanInterface $bban,
        string $countryCode
    ): self {
        self::validateCountryCodeFormat($countryCode);
        self::validateCountryCodeFormat($countryCode);
        self::validateSupportedCountry($countryCode);

        $checksum = self::validateChecksum($countryCode, '00', $bban);
        $checkDigit = 98 - (int) $checksum;
        $checkDigit = str_pad((string) $checkDigit, 2, '0', STR_PAD_LEFT);

        return new static($countryCode, $checkDigit, $bban);
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function ibanCheckDigits(): string
    {
        return $this->checkDigits;
    }

    public function bankCode(): string
    {
        return $this->bban->bankCode();
    }

    public function branchCode(): string
    {
        return $this->bban->branchCode();
    }

    public function countryCheckDigits(): string
    {
        return $this->bban->checkDigits();
    }

    public function accountNumber(): string
    {
        return $this->bban->accountNumber();
    }

    public function __toString(): string
    {
        $bbanString = $this->bban;

        return $this->countryCode . $this->checkDigits . $bbanString;
    }

    /** @throws InvalidArgumentException */
    private static function validateCountryCodeFormat(string $countryCode): void
    {
        if (!preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new InvalidArgumentException('The country code should be 2 letters');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateCheckDigitsFormat(string $checkDigits): void
    {
        if (!preg_match('/^[\d]{2}$/', $checkDigits)) {
            throw new InvalidArgumentException('The IBAN checksum must be 2 numeric characters');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateControlDigit(
        string $countryCode,
        string $checkDigits,
        Bban\BbanInterface $bban
    ): void {
        $checksum = self::validateChecksum($countryCode, $checkDigits, $bban);

        if ($checksum !== '01') {
            throw new InvalidArgumentException('The IBAN checksum digits are not valid');
        }
    }

    /** @throws InvalidArgumentException */
    private static function validateSupportedCountry(string $countryCode): void
    {
        if (!array_key_exists($countryCode, self::$countriesSupported)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The country code %s is not supported',
                    $countryCode
                )
            );
        }
    }

    private static function validateChecksum(
        string $countryCode,
        string $checkDigits,
        Bban\BbanInterface $bban
    ): string {
        $rearranged = $bban . $countryCode . $checkDigits;
        $digitsList = str_split($rearranged);

        $digitsList = array_map(['self', 'digitToInt'], $digitsList);
        $stringToCompute = implode('', $digitsList);

        $checksum = bcmod($stringToCompute, '97');

        return str_pad($checksum, 2, '0', STR_PAD_LEFT);
    }

    private static function digitToInt(string $value): int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return ord($value) - 55;
    }
}
