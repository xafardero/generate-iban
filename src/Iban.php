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

        if (! array_key_exists($countryCode, self::$countriesSupported)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The country code %s is not supported',
                    $countryCode
                )
            );
        }
        $bbanClass = self::$countriesSupported[$countryCode];

        /**
         * @var BbanInterface
         */
        $bban = $bbanClass::fromString($bbanString);

        return new static($countryCode, $checkDigits, $bban);
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
        $rearranged = strval($bban) . $countryCode . $checkDigits;
        $digitsList = str_split($rearranged);

        $digitsList = array_map(['self', 'digitToInt'], $digitsList);
        $stringToCompute = implode('', $digitsList);

        if (bcmod($stringToCompute, '97') !== '1') {
            throw new InvalidArgumentException('The IBAN checksum digits are not valid');
        }
    }

    /**
     * @param string $value
     *
     * @return int
     */
    private static function digitToInt($value)
    {
        if (is_numeric($value)) {
            return intval($value);
        }

        return ord($value) - 55;
    }
}
