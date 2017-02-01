<?php

namespace IbanGenerator\Tests;

use IbanGenerator\Bban\BbanInterface;
use IbanGenerator\Iban;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class IbanTest extends TestCase
{
    /**
     * @dataProvider validIbans
     *
     * @param $countryCode
     * @param $ibanChecksum
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     */
    public function testValidIban(
        $countryCode,
        $ibanChecksum,
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        $iban = new Iban($countryCode, $ibanChecksum, $bban);

        $this->assertEquals($countryCode, $iban->countryCode());
        $this->assertEquals($ibanChecksum, $iban->ibanCheckDigits());
        $this->assertEquals($bankCode, $iban->bankCode());
        $this->assertEquals($branchCode, $iban->branchCode());
        $this->assertEquals(
            $controlDigits,
            $iban->countryCheckDigits()
        );
        $this->assertEquals($bankAccount, $iban->accountNumber());
    }

    /**
     * @dataProvider validIbans
     *
     * @param $countryCode
     * @param $ibanChecksum
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     */
    public function testCreateFromValidString(
        $countryCode,
        $ibanChecksum,
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $stringIban = $countryCode . $ibanChecksum . $bankCode . $branchCode . $controlDigits . $bankAccount;
        $iban = Iban::fromString($stringIban);
        $this->assertEquals($stringIban, $iban->__toString());
    }

    /**
     * @dataProvider validIbans
     *
     * @param $countryCode
     * @param $ibanChecksum
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     */
    public function testCreateFromValidBban(
        $countryCode,
        $ibanChecksum,
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        $iban = Iban::fromBbanAndCountry($bban, 'ES');
        $this->assertEquals($ibanChecksum, $iban->ibanCheckDigits());
    }

    /**
     * @dataProvider notSupportedIbans
     *
     * @expectedException InvalidArgumentException
     *
     * @param $countryCode
     * @param $ibanChecksum
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     */
    public function testValidIbanWithNotSupportedCodeThrowsException(
        $countryCode,
        $ibanChecksum,
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $stringIban = $countryCode . $ibanChecksum . $bankCode . $branchCode . $controlDigits . $bankAccount;
        Iban::fromString($stringIban);
    }

    /**
     * @dataProvider invalidCountryCodes
     *
     * @expectedException InvalidArgumentException
     *
     * @param $countryCode
     * @param $ibanChecksum
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     */
    public function testInvalidCountryCodeThrowsException(
        $countryCode,
        $ibanChecksum,
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    /**
     * @dataProvider invalidControlDigitFormat
     *
     * @expectedException InvalidArgumentException
     *
     * @param $countryCode
     * @param $ibanChecksum
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     */
    public function testInvalidControlDigitFormatThrowsException(
        $countryCode,
        $ibanChecksum,
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    /**
     * @dataProvider invalidChecksum
     *
     * @expectedException InvalidArgumentException
     *
     * @param $countryCode
     * @param $ibanChecksum
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     */
    public function testInvalidChecksumThrowsException(
        $countryCode,
        $ibanChecksum,
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    /**
     * @return array
     */
    public function invalidCountryCodes()
    {
        return [
            ['', '68', '3841', '2436', '11', '6183191503'],
            ['C', '68', '3841', '2436', '11', '6183191503'],
            ['CAT', '68', '3841', '2436', '11', '6183191503'],
            ['C4', '68', '3841', '2436', '11', '6183191503'],
        ];
    }

    /**
     * @return array
     */
    public function invalidControlDigitFormat()
    {
        return [
            ['ES', '', '3841', '2436', '11', '6183191503'],
            ['ES', '7', '0989', '5990', '44', '6462241825'],
            ['ES', '756', '0989', '5990', '44', '6462241825'],
            ['ES', 'A9', '0989', '5990', '44', '6462241825'],
        ];
    }

    /**
     * @return array
     */
    public function invalidChecksum()
    {
        return [
            ['ES', '00', '3841', '2436', '11', '6183191503'],
            ['ES', '89', '0989', '5990', '44', '6462241825'],
        ];
    }

    /**
     * @return array
     */
    public function validIbans()
    {
        return [
            ['ES', '68', '3841', '2436', '11', '6183191503'],
            ['ES', '78', '0989', '5990', '44', '6462241825'],
        ];
    }

    /**
     * @return array
     */
    public function notSupportedIbans()
    {
        return [
            ['GB', '82', 'WEST', '12', '', '345698765432'],
        ];
    }

    /**
     * @param $bankCode
     * @param $branchCode
     * @param $controlDigits
     * @param $bankAccount
     *
     * @return BbanInterface
     */
    private function prophesizeBban(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        /**
         * @var ObjectProphecy|BbanInterface $bban
         */
        $bban = $this->prophesize('IbanGenerator\Bban\BbanInterface');
        $bban->bankCode()->willReturn($bankCode);
        $bban->branchCode()->willReturn($branchCode);
        $bban->checkDigits()->willReturn($controlDigits);
        $bban->accountNumber()->willReturn($bankAccount);
        $bban->__toString()
            ->willReturn($bankCode . $branchCode . $controlDigits . $bankAccount);

        return $bban->reveal();
    }
}
