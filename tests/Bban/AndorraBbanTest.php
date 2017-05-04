<?php

namespace IbanGenerator\Tests\Bban;

use IbanGenerator\Bban\AndorraBban;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class AndorraBbanTest extends TestCase
{
    /**
     * @dataProvider invalidBankCodes
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $bankAccount
     */
    public function testBankCodeShouldBe4NumericDigits(
        $bankCode,
        $branchCode,
        $bankAccount
    ) {
        new AndorraBban($bankCode, $branchCode, $bankAccount);
    }

    /**
     * @dataProvider invalidBranchCodes
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $bankAccount
     */
    public function testBranchCodeShouldBe4NumericDigits(
        $bankCode,
        $branchCode,
        $bankAccount
    ) {
        new AndorraBban($bankCode, $branchCode, $bankAccount);
    }

    /**
     * @dataProvider invalidBankAccounts
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $bankAccount
     */
    public function testBankAccountShouldBe10NumericDigits(
        $bankCode,
        $branchCode,
        $bankAccount
    ) {
        new AndorraBban($bankCode, $branchCode, $bankAccount);
    }

    /**
     * @dataProvider validSpanishBbans
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $bankAccount
     */
    public function testGetters(
        $bankCode,
        $branchCode,
        $bankAccount
    ) {
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

    /**
     * @dataProvider invalidBankStrings
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bbanString
     */
    public function testCreateFromStringMustHave20Digits($bbanString)
    {
        AndorraBban::fromString($bbanString);
    }

    /**
     * @dataProvider validSpanishBbans
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $bankAccount
     */
    public function testCreateFromStringWithValidAccountShouldReturnSpainBban(
        $bankCode,
        $branchCode,
        $bankAccount
    ) {
        $bbanString = $bankCode . $branchCode . $bankAccount;
        $bban = AndorraBban::fromString($bbanString);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals((string)$bban, $bbanString);
    }

    /**
     * @return array
     */
    public function invalidBankCodes()
    {
        return [
            ['', '2030', '200359100100'],
            ['1', '2030', '200359100100'],
            ['13521', '2030', '200359100100'],
            ['A445', '2030', '200359100100'],
        ];
    }

    /**
     * @return array
     */
    public function invalidBranchCodes()
    {
        return [
            ['0001', '', '200359100100'],
            ['0001', '5', '200359100100'],
            ['0001', '98725', '200359100100'],
            ['0001', '8X78', '200359100100'],
        ];
    }

    /**
     * @return array
     */
    public function invalidBankAccounts()
    {
        return [
            ['0001', '2030', '21', ''],
            ['0001', '2030', '24', '785123'],
            ['0001', '2030', '12', '21654872324654'],
            ['0001', '2030', '52', '875A2456J2'],
        ];
    }

    /***
     * @return array
     */
    public function validSpanishBbans()
    {
        return [
            ['0001', '2030', '200359100100'],
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
