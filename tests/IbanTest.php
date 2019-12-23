<?php

declare(strict_types=1);

namespace IbanGenerator\Tests;

use IbanGenerator\Bban\BbanInterface;
use IbanGenerator\Iban;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class IbanTest extends TestCase
{
    /** @dataProvider validIbans */
    public function testValidIban(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        $iban = new Iban($countryCode, $ibanChecksum, $bban);

        $this->assertEquals(strtoupper($countryCode), $iban->countryCode());
        $this->assertEquals($ibanChecksum, $iban->ibanCheckDigits());
        $this->assertEquals($bankCode, $iban->bankCode());
        $this->assertEquals($branchCode, $iban->branchCode());
        $this->assertEquals(
            $controlDigits,
            $iban->countryCheckDigits()
        );
        $this->assertEquals($bankAccount, $iban->accountNumber());
    }

    /** @dataProvider validIbans */
    public function testCreateFromValidString(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $stringIban = $countryCode . $ibanChecksum . $bankCode . $branchCode . $controlDigits . $bankAccount;
        $iban = Iban::fromString($stringIban);
        $this->assertEquals(strtoupper($stringIban), $iban->__toString());
    }

    /** @dataProvider validIbans */
    public function testCreateFromValidBban(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        $iban = Iban::fromBbanAndCountry($bban, $countryCode);
        $this->assertEquals($ibanChecksum, $iban->ibanCheckDigits());
    }

    /** @dataProvider notSupportedIbans */
    public function testValidIbanWithNotSupportedCodeThrowsException(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $stringIban = $countryCode . $ibanChecksum . $bankCode . $branchCode . $controlDigits . $bankAccount;
        Iban::fromString($stringIban);
    }

    /** @dataProvider invalidCountryCodes */
    public function testInvalidCountryCodeThrowsException(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    /** @dataProvider invalidControlDigitFormat */
    public function testInvalidControlDigitFormatThrowsException(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    /** @dataProvider invalidChecksum */
    public function testInvalidChecksumThrowsException(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    public function invalidCountryCodes(): array
    {
        return [
            ['', '68', '3841', '2436', '11', '6183191503'],
            ['C', '68', '3841', '2436', '11', '6183191503'],
            ['CAT', '68', '3841', '2436', '11', '6183191503'],
            ['C4', '68', '3841', '2436', '11', '6183191503'],
        ];
    }

    public function invalidControlDigitFormat(): array
    {
        return [
            ['ES', '', '3841', '2436', '11', '6183191503'],
            ['ES', '7', '0989', '5990', '44', '6462241825'],
            ['ES', '756', '0989', '5990', '44', '6462241825'],
            ['ES', 'A9', '0989', '5990', '44', '6462241825'],
        ];
    }

    public function invalidChecksum(): array
    {
        return [
            ['ES', '00', '3841', '2436', '11', '6183191503'],
            ['ES', '89', '0989', '5990', '44', '6462241825'],
        ];
    }

    public function validIbans(): array
    {
        return [
            ['ES', '68', '3841', '2436', '11', '6183191503'],
            ['ES', '78', '0989', '5990', '44', '6462241825'],
            ['ES', '72', '0081', '0052', '00', '0004400044'],
            ['ES', '31', '0049', '1806', '95', '2811869099'],
            ['ES', '18', '2080', '0769', '75', '3040000478'],
            ['ES', '09', '0182', '6035', '49', '0000748708'],
            ['ES', '83', '2048', '0000', '27', '3400106773'],
            ['ES', '24', '2038', '0603', '29', '6005700064'],
            ['ES', '09', '2103', '2034', '25', '0030003000'],
            ['ES', '57', '2100', '3063', '99', '2200110010'],
            ['ES', '53', '1491', '0001', '28', '1008158220'],
            ['ES', '27', '2095', '0264', '60', '9105878176'],
        ];
    }

    public function notSupportedIbans(): array
    {
        return [
            ['GB', '82', 'WEST', '12', '', '345698765432'],
        ];
    }

    public function notSupportedIbansWithWrongLength(): array
    {
        return [
            ['ES', '78', '2', '5990', '44', '3'],
            ['ES', '78', '2', '5990', '34', '3'],
            ['ES', '0', '0', '0', '0', '0'],
        ];
    }

    private function prophesizeBban(
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): BbanInterface {
        /** @var ObjectProphecy|BbanInterface $bban */
        $bban = $this->prophesize(BbanInterface::class);
        $bban->bankCode()->willReturn($bankCode);
        $bban->branchCode()->willReturn($branchCode);
        $bban->checkDigits()->willReturn($controlDigits);
        $bban->accountNumber()->willReturn($bankAccount);
        $bban->__toString()
            ->willReturn($bankCode . $branchCode . $controlDigits . $bankAccount);

        return $bban->reveal();
    }

    /** @dataProvider notSupportedIbansWithWrongLength */
    public function testInvalidIbanWithInvalidArgumentException(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $stringIban = $countryCode . $ibanChecksum . $bankCode . $branchCode . $controlDigits . $bankAccount;
        Iban::fromString($stringIban);
    }
}
