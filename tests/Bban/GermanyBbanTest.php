<?php

namespace IbanGenerator\Tests\Bban;

use IbanGenerator\Bban\GermanyBban;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GermanyBbanTest extends TestCase
{
    /**
     * @dataProvider invalidBankCodes
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $bankAccount
     */
    public function testBankCodeShouldBe8NumericDigits(
        $bankCode,
        $bankAccount
    ) {
        new GermanyBban($bankCode, $bankAccount);
    }

    /**
     * @dataProvider invalidBankAccounts
     *
     * @expectedException InvalidArgumentException
     *
     * @param string $bankCode
     * @param string $bankAccount
     */
    public function testBankAccountShouldBe10NumericDigits(
        $bankCode,
        $bankAccount
    ) {
        new GermanyBban($bankCode, $bankAccount);
    }

    /**
     * @dataProvider validGermanBbans
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
        $bban = new GermanyBban(
            $bankCode,
            $bankAccount
        );
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($controlDigits, $bban->checkDigits());
        $this->assertEquals($bankAccount, $bban->accountNumber());
    }

    /**
     * @dataProvider validGermanBbans
     *
     * @param string $bankCode
     * @param string $branchCode
     * @param string $controlDigits
     * @param string $bankAccount
     */
    public function testCreateFromStringWithValidAccountShouldReturnGermanyBban(
        $bankCode,
        $branchCode,
        $controlDigits,
        $bankAccount
    ) {
        $bbanString = $bankCode . $branchCode . $controlDigits . $bankAccount;
        $bban = GermanyBban::fromString($bbanString);
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

    /*
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

    /***
     * @return array
     */
    public function validGermanBbans()
    {
        return [
            ['50010517', '', '', '5425194396'],
            ['50010517', '', '', '9753548973'],
            ['50010517', '', '', '5739216414'],
            ['50010517', '', '', '4966436663'],
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
