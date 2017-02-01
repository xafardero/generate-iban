<?php

namespace IbanGenerator;

use IbanGenerator\Bban\BbanInterface;
use IbanGenerator\Bban\SpainBban;
use InvalidArgumentException;

class Iban
{
    /**
     * @var BbanInterface
     */
    private $bban;

    /**
     * @var string
     */
    private $countryCode;

    /**
     * @var string
     */
    private $checkDigits;

    /**
     * @var array
     */
    private static $countriesSupported = [
        'ES' => SpainBban::class,
    ];

    /**
     * Iban constructor.
     *
     * @param string $countryCode
     * @param string $checkDigits
     * @param BbanInterface $bban
     *
     * @throws InvalidArgumentException
     */
    public function __construct($countryCode, $checkDigits, BbanInterface $bban)
    {
        $countryCode = strtoupper($countryCode);
        static::validateCountryCodeFormat($countryCode);
        static::validateCheckDigitsFormat($checkDigits);
        static::validateControlDigit($countryCode, $checkDigits, $bban);
        $this->countryCode = $countryCode;
        $this->checkDigits = $checkDigits;
        $this->bban = $bban;
    }

    /**
     * @param string $iban
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public static function fromString($iban)
    {
        if (! preg_match('/^[0-9A-Z]{16,34}$/', $iban)) {
            throw new InvalidArgumentException('Iban should be between 16 and 34 characters');
        }

        $countryCode = substr($iban, 0, 2);
        $checkDigits = substr($iban, 2, 2);
        $bbanString = substr($iban, 4);

        self::validateSupportedCountry($countryCode);
        $bbanClass = self::$countriesSupported[$countryCode];

        /**
         * @var BbanInterface
         */
        $bban = $bbanClass::fromString($bbanString);

        return new static($countryCode, $checkDigits, $bban);
    }

    /**
     * @param BbanInterface $bban
     * @param string $countryCode
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public static function fromBbanAndCountry(BbanInterface $bban, $countryCode)
    {
        static::validateCountryCodeFormat($countryCode);
        static::validateCountryCodeFormat($countryCode);
        static::validateSupportedCountry($countryCode);

        $checksum = self::validateChecksum($countryCode, '00', $bban);
        $checkDigit = 98 - (int) $checksum;
        $checkDigit = str_pad($checkDigit, 2, 0, STR_PAD_LEFT);

        return new static($countryCode, $checkDigit, $bban);
    }

    /**
     * @return string
     */
    public function countryCode()
    {
        return $this->countryCode;
    }

    /**
     * @return string
     */
    public function ibanCheckDigits()
    {
        return $this->checkDigits;
    }

    /**
     * @return string
     */
    public function bankCode()
    {
        return $this->bban->bankCode();
    }

    /**
     * @return string
     */
    public function branchCode()
    {
        return $this->bban->branchCode();
    }

    /**
     * @return string
     */
    public function countryCheckDigits()
    {
        return $this->bban->checkDigits();
    }

    /**
     * @return string
     */
    public function accountNumber()
    {
        return $this->bban->accountNumber();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $bbanString = $this->bban;

        return $this->countryCode . $this->checkDigits . $bbanString;
    }

    /**
     * @param $countryCode
     *
     * @throws InvalidArgumentException
     */
    private static function validateCountryCodeFormat($countryCode)
    {
        if (! preg_match('/^[A-Z]{2}$/', $countryCode)) {
            throw new InvalidArgumentException('The country code should be 2 letters');
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
            throw new InvalidArgumentException('The IBAN checksum must be 2 numeric characters');
        }
    }

    /**
     * @param string $countryCode
     * @param string $checkDigits
     * @param BbanInterface $bban
     *
     * @throws InvalidArgumentException
     */
    private static function validateControlDigit(
        $countryCode,
        $checkDigits,
        BbanInterface $bban
    ) {
        $checksum = self::validateChecksum($countryCode, $checkDigits, $bban);

        if ($checksum !== '01') {
            throw new InvalidArgumentException('The IBAN checksum digits are not valid');
        }
    }

    /**
     * @param $countryCode
     *
     * @throws InvalidArgumentException
     */
    private static function validateSupportedCountry($countryCode)
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

    /**
     * @param $countryCode
     * @param $checkDigits
     * @param BbanInterface $bban
     *
     * @return string
     */
    private static function validateChecksum($countryCode, $checkDigits, BbanInterface $bban)
    {
        $rearranged = (string) $bban . $countryCode . $checkDigits;
        $digitsList = str_split($rearranged);

        $digitsList = array_map(['self', 'digitToInt'], $digitsList);
        $stringToCompute = implode('', $digitsList);

        $checksum = bcmod($stringToCompute, '97');

        return str_pad($checksum, 2, 0, STR_PAD_LEFT);
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private static function digitToInt($value)
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        return ord($value) - 55;
    }
}
