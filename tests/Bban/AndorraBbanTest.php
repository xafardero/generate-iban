<?php

declare(strict_types=1);

namespace IbanGenerator\Tests\Bban;

use IbanGenerator\Bban\AndorraBban;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AndorraBbanTest extends TestCase
{
    /** @dataProvider invalidBankCodes */
    public function testBankCodeShouldBe4NumericDigits(
        string $bankCode,
        string $branchCode,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new AndorraBban($bankCode, $branchCode, $bankAccount);
    }

    /** @dataProvider invalidBranchCodes */
    public function testBranchCodeShouldBe4NumericDigits(
        string $bankCode,
        string $branchCode,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new AndorraBban($bankCode, $branchCode, $bankAccount);
    }

    /** @dataProvider invalidBankAccounts */
    public function testBankAccountShouldBe10NumericDigits(
        string $bankCode,
        string $branchCode,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new AndorraBban($bankCode, $branchCode, $bankAccount);
    }

    /** @dataProvider validSpanishBbans */
    public function testGetters(
        string $bankCode,
        string $branchCode,
        string $bankAccount
    ): void {
        $bban = new AndorraBban(
            $bankCode,
            $branchCode,
            $bankAccount
        );
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals(null, $bban->checkDigits());
    }

    /** @dataProvider invalidBankStrings */
    public function testCreateFromStringMustHave20Digits(string $bbanString): void
    {
        $this->expectException(InvalidArgumentException::class);

        AndorraBban::fromString($bbanString);
    }

    /** @dataProvider validSpanishBbans */
    public function testCreateFromStringWithValidAccountShouldReturnSpainBban(
        string $bankCode,
        string $branchCode,
        string $bankAccount
    ): void {
        $bbanString = $bankCode . $branchCode . $bankAccount;
        $bban = AndorraBban::fromString($bbanString);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals((string) $bban, $bbanString);
    }

    public function invalidBankCodes(): array
    {
        return [
            ['', '2030', '200359100100'],
            ['1', '2030', '200359100100'],
            ['13521', '2030', '200359100100'],
            ['A445', '2030', '200359100100'],
        ];
    }

    public function invalidBranchCodes(): array
    {
        return [
            ['0001', '', '200359100100'],
            ['0001', '5', '200359100100'],
            ['0001', '98725', '200359100100'],
            ['0001', '8X78', '200359100100'],
        ];
    }

    public function invalidBankAccounts(): array
    {
        return [
            ['0001', '2030', '21', ''],
            ['0001', '2030', '24', '785123'],
            ['0001', '2030', '12', '21654872324654'],
            ['0001', '2030', '52', '875A2456J2'],
        ];
    }

    public function validSpanishBbans(): array
    {
        return [
            ['0001', '2030', '200359100100'],
        ];
    }

    public function invalidBankStrings(): array
    {
        return [
            ['2085206664030008280'],
            ['004923520724142054185'],
            ['210004184A0200051332'],
        ];
    }
}
