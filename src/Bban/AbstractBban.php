<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use IbanGenerator\Bban\Exception\MethodNotSupportedException;

abstract class AbstractBban implements BbanInterface
{
    /** @var string */
    protected $bankCode;
    /** @var string */
    protected $accountNumber;

    public function bankCode(): string
    {
        return $this->bankCode;
    }

    public function accountNumber(): string
    {
        return $this->accountNumber;
    }

    /** @throws MethodNotSupportedException */
    public function branchCode(): string
    {
        throw new MethodNotSupportedException('This Bban does not support branch code');
    }

    /** @throws MethodNotSupportedException */
    public function checkDigits(): string
    {
        throw new MethodNotSupportedException('This Bban does not support check digits');
    }

    /** @throws MethodNotSupportedException */
    public function accountType(): string
    {
        throw new MethodNotSupportedException('This Bban does not support account type');
    }
}
