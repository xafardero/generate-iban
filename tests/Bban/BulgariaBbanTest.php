<?php

declare(strict_types=1);

namespace IbanGenerator\Tests\Bban;

use Exception;
use IbanGenerator\Bban\BulgariaBban;
use IbanGenerator\Bban\Exception\MethodNotSupportedException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BulgariaBbanTest extends TestCase
{
    /** @dataProvider invalidBankCodes */
    public function testBankCodeShouldBe4AlphabeticalCharacters(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $accountNumber
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BulgariaBban($bankCode, $branchCode, $accountType, $accountNumber);
    }

    /** @dataProvider invalidBranchCodes */
    public function testBranchCodeShouldBe4NumericDigits(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $accountNumber
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BulgariaBban($bankCode, $branchCode, $accountType, $accountNumber);
    }

    /** @dataProvider invalidAccountTypes */
    public function testAccountTypeShouldBe2NumericDigits(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $accountNumber
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BulgariaBban($bankCode, $branchCode, $accountType, $accountNumber);
    }

    /** @dataProvider invalidBankAccounts */
    public function testBankAccountShouldBe8AlphanumericalDigits(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $accountNumber
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BulgariaBban($bankCode, $branchCode, $accountType, $accountNumber);
    }

    /** @dataProvider validBulgariaBbans */
    public function testGetters(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $accountNumber
    ): void {
        $bban = new BulgariaBban($bankCode, $branchCode, $accountType, $accountNumber);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($accountNumber, $bban->accountNumber());
        $this->assertEquals($accountType, $bban->accountType());

        try {
            $bban->checkDigits();
            $this->fail('CheckDigits getter should not be supported for this Bban');
        } catch (Exception $exception) {
            $this->assertInstanceOf(MethodNotSupportedException::class, $exception);
        }
    }

    /** @dataProvider invalidBankStrings */
    public function testCreateFromStringMustHave20Digits(string $bbanString): void
    {
        $this->expectException(InvalidArgumentException::class);

        BulgariaBban::fromString($bbanString);
    }

    /** @dataProvider validBulgariaBbans */
    public function testCreateFromStringWithValidAccountShouldReturnBulgariaBban(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $accountNumber
    ): void {
        $bbanString = $bankCode . $branchCode . $accountType . $accountNumber;
        $bban = BulgariaBban::fromString($bbanString);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($accountType, $bban->accountType());
        $this->assertEquals($accountNumber, $bban->accountNumber());
        $this->assertEquals((string) $bban, $bbanString);
    }

    public function validBulgariaBbans(): array
    {
        return [
            ['STSA', '9300', '31', '63575284'],
            ['FINV', '9159', '19', 'VARCHEV1'],
            ['STSA', '9300', '11', '54994279'],
        ];
    }

    public function invalidBankCodes(): array
    {
        return [
            ['', '9300', '11', '54994279'],
            ['ST', '9300', '11', '54994279'],
            ['STSAH', '9300', '11', '54994279'],
            ['1234', '9300', '11', '54994279'],
        ];
    }

    public function invalidBranchCodes(): array
    {
        return [
            ['STSA', '', '11', '54994279'],
            ['STSA', '93', '11', '54994279'],
            ['STSA', '93008', '11', '54994279'],
            ['STSA', '93G0', '11', '54994279'],
        ];
    }

    public function invalidAccountTypes(): array
    {
        return [
            ['STSA', '9300', '', '54994279'],
            ['STSA', '9300', '1', '54994279'],
            ['STSA', '9300', '116', '54994279'],
            ['STSA', '9300', '1G', '54994279'],
        ];
    }

    public function invalidBankAccounts(): array
    {
        return [
            ['STSA', '9300', '11', ''],
            ['STSA', '9300', '11', '5499'],
            ['STSA', '9300', '11', '549942796'],
        ];
    }

    public function invalidBankStrings(): array
    {
        return [
            ['123593003163575284'],
            ['STSAAGDF3163575284'],
            ['STSA9300F163575284'],
            ['STSA9300316357528'],
            ['STSA930031635752840'],
        ];
    }
}
