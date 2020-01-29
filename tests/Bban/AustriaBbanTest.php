<?php

declare(strict_types=1);

namespace IbanGenerator\Tests\Bban;

use Exception;
use IbanGenerator\Bban\AustriaBban;
use IbanGenerator\Bban\Exception\MethodNotSupportedException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AustriaBbanTest extends TestCase
{
    /** @dataProvider invalidBankCodes */
    public function testBankCodeShouldBe5NumericDigits(
        string $bankCode,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new AustriaBban($bankCode, $bankAccount);
    }

    /** @dataProvider invalidBankAccounts */
    public function testBankAccountShouldBe11NumericDigits(
        string $bankCode,
        string $bankAccount
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new AustriaBban($bankCode, $bankAccount);
    }

    /** @dataProvider validAustriaBbans */
    public function testGetters(
        string $bankCode,
        string $bankAccount
    ): void {
        $bban = new AustriaBban(
            $bankCode,
            $bankAccount
        );
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($bankAccount, $bban->accountNumber());

        try {
            $bban->branchCode();
            $this->fail('BranchCode getter should not be supported for this Bban');
        } catch (Exception $exception) {
            $this->assertInstanceOf(MethodNotSupportedException::class, $exception);
        }

        try {
            $bban->checkDigits();
            $this->fail('CheckDigits getter should not be supported for this Bban');
        } catch (Exception $exception) {
            $this->assertInstanceOf(MethodNotSupportedException::class, $exception);
        }

        try {
            $bban->accountType();
            $this->fail('AccountType getter should not be supported for this Bban');
        } catch (Exception $exception) {
            $this->assertInstanceOf(MethodNotSupportedException::class, $exception);
        }
    }

    /** @dataProvider invalidBankStrings */
    public function testCreateFromStringMustHave20Digits(string $bbanString): void
    {
        $this->expectException(InvalidArgumentException::class);

        AustriaBban::fromString($bbanString);
    }

    /** @dataProvider validAustriaBbans */
    public function testCreateFromStringWithValidAccountShouldReturnAustriaBban(
        string $bankCode,
        string $bankAccount
    ): void {
        $bbanString = $bankCode . $bankAccount;
        $bban = AustriaBban::fromString($bbanString);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals((string) $bban, $bbanString);
    }

    public function invalidBankCodes(): array
    {
        return [
            ['', '56552296719'],
            ['2250', '56552296719'],
            ['225080', '56552296719'],
            ['225A0', '56552296719'],
        ];
    }

    public function invalidBankAccounts(): array
    {
        return [
            ['22500', ''],
            ['22500', '5655229671'],
            ['22500', '565522967197'],
            ['22500', '565522J6719'],
        ];
    }

    public function validAustriaBbans(): array
    {
        return [
            ['22500', '56552296719'],
        ];
    }

    public function invalidBankStrings(): array
    {
        return [
            ['225005655229671'],
            ['22500565522967198'],
            ['225005J552296719'],
        ];
    }
}
