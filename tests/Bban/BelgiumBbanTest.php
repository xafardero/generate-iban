<?php

declare(strict_types=1);

namespace IbanGenerator\Tests\Bban;

use Exception;
use IbanGenerator\Bban\BelgiumBban;
use IbanGenerator\Bban\Exception\MethodNotSupportedException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class BelgiumBbanTest extends TestCase
{
    /** @dataProvider invalidBankCodes */
    public function testBankCodeShouldBe3NumericDigits(
        string $bankCode,
        string $bankAccount,
        string $controlDigits
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BelgiumBban($bankCode, $bankAccount, $controlDigits);
    }

    /** @dataProvider invalidBankAccounts */
    public function testBankAccountShouldBe7NumericDigits(
        string $bankCode,
        string $bankAccount,
        string $controlDigits
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BelgiumBban($bankCode, $bankAccount, $controlDigits);
    }

    /** @dataProvider invalidCheckDigitsFormat */
    public function testCheckDigitsShouldBe2NumericDigits(
        string $bankCode,
        string $bankAccount,
        string $controlDigits
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BelgiumBban($bankCode, $bankAccount, $controlDigits);
    }

    /** @dataProvider invalidCheckDigitsValidation */
    public function testCheckDigitsShouldBeValid(
        string $bankCode,
        string $bankAccount,
        string $controlDigits
    ): void {
        $this->expectException(InvalidArgumentException::class);

        new BelgiumBban($bankCode, $bankAccount, $controlDigits);
    }

    /** @dataProvider validBelgiumBbans */
    public function testGetters(
        string $bankCode,
        string $bankAccount,
        string $controlDigits
    ): void {
        $bban = new BelgiumBban($bankCode, $bankAccount, $controlDigits);

        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals($controlDigits, $bban->checkDigits());

        try {
            $bban->accountType();
            $this->fail('AccountType getter should not be supported for this Bban');
        } catch (Exception $exception) {
            $this->assertInstanceOf(MethodNotSupportedException::class, $exception);
        }
    }

    /** @dataProvider invalidCheckDigitsValidation */
    public function testCreateFromStringMustHave12Digits(string $bbanString): void
    {
        $this->expectException(InvalidArgumentException::class);

        BelgiumBban::fromString($bbanString);
    }

    /** @dataProvider validBelgiumBbans */
    public function testCreateFromStringWithValidAccountShouldReturnBelgiumBban(
        string $bankCode,
        string $bankAccount,
        string $controlDigits
    ): void {
        $bbanString = $bankCode . $bankAccount . $controlDigits;
        $bban = BelgiumBban::fromString($bbanString);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals($controlDigits, $bban->checkDigits());
        $this->assertEquals((string) $bban, $bbanString);
    }

    public function invalidBankCodes(): array
    {
        return [
            ['', '3917149', '43'],
            ['4', '3917149', '43'],
            ['9549', '3917149', '43'],
            ['9J4', '3917149', '43'],
        ];
    }

    public function invalidBankAccounts(): array
    {
        return [
            ['954', '', '43'],
            ['954', '391714', '43'],
            ['954', '39171498', '43'],
            ['954', '391J149', '43'],
        ];
    }

    public function invalidCheckDigitsFormat(): array
    {
        return [
            ['954', '3917149', ''],
            ['954', '3917149', '4'],
            ['954', '3917149', '438'],
            ['954', '3917149', '4K'],
        ];
    }

    public function invalidCheckDigitsValidation(): array
    {
        return [
            ['954', '3917149', '42'],
        ];
    }

    public function validBelgiumBbans(): array
    {
        return [
            ['954', '3917149', '43'],
        ];
    }

    public function invalidBankStrings(): array
    {
        return [
            ['95439171494'],
            ['9543917149437'],
            ['954391H14943'],
        ];
    }
}
