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
    /** @dataProvider validIbansSplitted */
    public function testValidIban(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount,
        string $accountType
    ): void {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount,
            $accountType
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
        $this->assertEquals($accountType, $iban->accountType());
    }

    /** @dataProvider validIbans */
    public function testCreateFromValidString(
        string $stringIban
    ): void {
        $iban = Iban::fromString($stringIban);
        $this->assertEquals(strtoupper($stringIban), (string) $iban);
    }

    /** @dataProvider validIbansSplitted */
    public function testCreateFromValidBban(
        string $countryCode,
        string $ibanChecksum,
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount,
        string $accountType
    ): void {
        $bban = $this->prophesizeBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount,
            $accountType
        );

        $iban = Iban::fromBbanAndCountry($bban, $countryCode);
        $this->assertEquals($ibanChecksum, $iban->ibanCheckDigits());
    }

    /** @dataProvider notSupportedIbans */
    public function testValidIbanWithNotSupportedCodeThrowsException(
        string $stringIban
    ): void {
        $this->expectException(InvalidArgumentException::class);

        Iban::fromString($stringIban);
    }

    /** @dataProvider invalidCountryCodes */
    public function testInvalidCountryCodeThrowsException(
        string $countryCode,
        string $ibanChecksum,
        string $bbanString
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $bban = $this->prophesizeBbanFromString($bbanString);

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    /** @dataProvider invalidControlDigitFormat */
    public function testInvalidControlDigitFormatThrowsException(
        string $countryCode,
        string $ibanChecksum,
        string $bbanString
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $bban = $this->prophesizeBbanFromString($bbanString);

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    /** @dataProvider invalidChecksum */
    public function testInvalidChecksumThrowsException(
        string $countryCode,
        string $ibanChecksum,
        string $bbanString
    ): void {
        $this->expectException(InvalidArgumentException::class);

        $bban = $this->prophesizeBbanFromString($bbanString);

        new Iban($countryCode, $ibanChecksum, $bban);
    }

    public function invalidCountryCodes(): array
    {
        return [
            ['', '68', '38412436116183191503'],
            ['C', '68', '38412436116183191503'],
            ['CAT', '68', '38412436116183191503'],
            ['C4', '68', '38412436116183191503'],
        ];
    }

    public function invalidControlDigitFormat(): array
    {
        return [
            ['ES', '', '38412436116183191503'],
            ['ES', '7', '38412436116183191503'],
            ['ES', '756', '38412436116183191503'],
            ['ES', 'A9', '38412436116183191503'],
        ];
    }

    public function invalidChecksum(): array
    {
        return [
            ['ES', '00', '38412436116183191503'],
            ['ES', '89', '09895990446462241825'],
        ];
    }

    public function validIbansSplitted(): array
    {
        return [
            ['ES', '68', '3841', '2436', '11', '6183191503', ''],
            ['ES', '78', '0989', '5990', '44', '6462241825', ''],
            ['ES', '72', '0081', '0052', '00', '0004400044', ''],
            ['ES', '31', '0049', '1806', '95', '2811869099', ''],
            ['ES', '18', '2080', '0769', '75', '3040000478', ''],
            ['ES', '09', '0182', '6035', '49', '0000748708', ''],
            ['ES', '83', '2048', '0000', '27', '3400106773', ''],
            ['ES', '24', '2038', '0603', '29', '6005700064', ''],
            ['es', '09', '2103', '2034', '25', '0030003000', ''],
            ['eS', '57', '2100', '3063', '99', '2200110010', ''],
            ['Es', '53', '1491', '0001', '28', '1008158220', ''],
            ['ES', '27', '2095', '0264', '60', '9105878176', ''],
        ];
    }

    public function validIbans(): array
    {
        return [
            ['ES6838412436116183191503'],
            ['ES7809895990446462241825'],
            ['ES7200810052000004400044'],
            ['ES3100491806952811869099'],
            ['ES1820800769753040000478'],
            ['ES0901826035490000748708'],
            ['ES8320480000273400106773'],
            ['ES2420380603296005700064'],
            ['es0921032034250030003000'],
            ['eS5721003063992200110010'],
            ['Es5314910001281008158220'],
            ['ES2720950264609105878176'],
            ['BG04STSA93003163575284'],
            ['BG10FINV915919VARCHEV1'],
            ['AT342250056552296719'],
            ['AT231947031765951149'],
        ];
    }

    public function notSupportedIbans(): array
    {
        return [
            ['GB82WEST12345698765432'],
        ];
    }

    public function notSupportedIbansWithWrongLength(): array
    {
        return [
            ['ES7825990443'],
            ['ES7825990343'],
            ['ES00000'],
        ];
    }

    private function prophesizeBban(
        string $bankCode,
        string $branchCode,
        string $controlDigits,
        string $bankAccount,
        string $accountType
    ): BbanInterface {
        /** @var ObjectProphecy|BbanInterface $bban */
        $bban = $this->prophesize(BbanInterface::class);
        $bban->bankCode()->willReturn($bankCode);
        $bban->branchCode()->willReturn($branchCode);
        $bban->checkDigits()->willReturn($controlDigits);
        $bban->accountNumber()->willReturn($bankAccount);
        $bban->accountType()->willReturn($accountType);
        $bban->__toString()
            ->willReturn($bankCode . $branchCode . $controlDigits . $bankAccount);

        return $bban->reveal();
    }

    /** @dataProvider notSupportedIbansWithWrongLength */
    public function testInvalidIbanWithInvalidArgumentException(
        string $stringIban
    ): void {
        $this->expectException(InvalidArgumentException::class);

        Iban::fromString($stringIban);
    }

    private function prophesizeBbanFromString(string $bbanString): BbanInterface
    {
        /** @var ObjectProphecy|BbanInterface $bbanProphet */
        $bbanProphet = $this->prophesize(BbanInterface::class);
        $bbanProphet->__toString()->willReturn($bbanString);

        return $bbanProphet->reveal();
    }
}
