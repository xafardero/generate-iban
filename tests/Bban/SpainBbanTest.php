<?php

namespace IbanGenerator\Tests\Bban;

use IbanGenerator\Bban\SpainBban;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class SpainBbanTest extends TestCase
{
    /**
     * @dataProvider invalidBankCodes
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testBankCodeShouldBe4NumericDigits(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        new SpainBban($bankCode, $branchCode, $controlDigits, $bankAccount);
    }

    /**
     * @dataProvider invalidBranchCodes
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testBranchCodeShouldBe4NumericDigits(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        new SpainBban($bankCode, $branchCode, $controlDigits, $bankAccount);
    }

    /**
     * @dataProvider invalidCheckDigitsFormat
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testCheckDigitsShouldBe2NumericDigits(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        new SpainBban($bankCode, $branchCode, $controlDigits, $bankAccount);
    }

    /**
     * @dataProvider invalidBankAccounts
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testBankAccountShouldBe10NumericDigits(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        new SpainBban($bankCode, $branchCode, $controlDigits, $bankAccount);
    }

    /**
     * @dataProvider invalidCheckDigitsValidation
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testCheckDigitsShouldBeValid(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        new SpainBban($bankCode, $branchCode, $controlDigits, $bankAccount);
    }

    /**
     * @dataProvider validSpanishBbans
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testGetters(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bban = new SpainBban(
            $bankCode,
            $branchCode,
            $controlDigits,
            $bankAccount
        );
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($controlDigits, $bban->checkDigits());
        $this->assertEquals($bankAccount, $bban->accountNumber());
    }

    /**
     * @dataProvider invalidCheckDigitsValidation
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bbanString
     */
    public function testCreateFromStringMustHave20Digits($bbanString)
    {
        SpainBban::fromString($bbanString);
    }

    /**
     * @dataProvider validSpanishBbans
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testCreateFromStringWithValidAccountShouldReturnSpainBban(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bbanString = $bankCode . $branchCode . $controlDigits . $bankAccount;
        $bban = SpainBban::fromString($bbanString);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($controlDigits, $bban->checkDigits());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals(strval($bban), $bbanString);
    }

    /**
     * @return array
     */
    public function invalidBankCodes()
    {
        return [
            ['', '6545', '21', '8754292156'],
            ['1', '6248', '24', '7851235423'],
            ['13521', '8723', '12', '2165487232'],
            ['A445', '2354', '52', '8753245682'],
        ];
    }

    /**
     * @return array
     */
    public function invalidBranchCodes()
    {
        return [
            ['1232', '', '21', '8754292156'],
            ['1234', '5', '24', '7851235423'],
            ['1234', '98725', '12', '2165487232'],
            ['1234', '8X78', '52', '8753245682'],
        ];
    }

    /**
     * @return array
     */
    public function invalidCheckDigitsFormat()
    {
        return [
            ['1232', '2135', '', '8754292156'],
            ['1234', '4654', '5', '7851235423'],
            ['1234', '8795', '128', '2165487232'],
            ['1234', '2154', 'A3', '8753245682'],
        ];
    }

    /**
     * @return array
     */
    public function invalidBankAccounts()
    {
        return [
            ['1232', '2135', '21', ''],
            ['1234', '4654', '24', '785123'],
            ['1234', '8795', '12', '21654872324654'],
            ['1234', '2154', '52', '875A2456J2'],
        ];
    }

    /**
     * @return array
     */
    public function invalidCheckDigitsValidation()
    {
        return [
            ['8936', '0405', '21', '0341590132'],
            ['9544', '5361', '24', '6157913076'],
            ['4261', '0515', '12', '8903671466'],
            ['8423', '7363', '52', '4842578607'],
        ];
    }

    /***
     * @return array
     */
    public function validSpanishBbans()
    {
        return [
            ['0030', '2053', '02', '0000875271'],
            ['0049', '1500', '04', '2710151321'],
            ['2080', '5801', '14', '3040000499'],
            ['0024', '6912', '50', '0600865953'],
        ];
    }

    /***
     * @return array
     */
    public function invalidBankStrings()
    {
        return [
            ['2085206664030008280'],
            ['004923520724142054185'],
            ['210004184A0200051332'],
        ];
    }
}
