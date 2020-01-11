<?php

declare(strict_types=1);

namespace IbanGenerator\Tests\Bban;

use IbanGenerator\Bban\BulgariaBban;
use PHPUnit\Framework\TestCase;

class BulgariaBbanTest extends TestCase
{
    /** @dataProvider validBulgariaBbans */
    public function testCreateFromStringWithValidAccountShouldReturnBulgariaBban(
        string $bankCode,
        string $branchCode,
        string $accountType,
        string $bankAccount
    ): void {
        $bbanString = $bankCode . $branchCode . $accountType . $bankAccount;
        $bban = BulgariaBban::fromString($bbanString);
        $this->assertEquals($bankCode, $bban->bankCode());
        $this->assertEquals($branchCode, $bban->branchCode());
        $this->assertEquals($accountType, $bban->accountType());
        $this->assertEquals($bankAccount, $bban->accountNumber());
        $this->assertEquals((string) $bban, $bbanString);
    }

    public function validBulgariaBbans(): array
    {
        return [
            ['STSA', '9300', '31', '63575284'],
            ['FINV', '9159', '19', 'VARCHEV1'],
        ];
    }
}
