<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use IbanGenerator\Bban\Exception\MethodNotSupportedException;
use InvalidArgumentException;

interface BbanInterface
{
    /** @throws InvalidArgumentException */
    public static function fromString(string $bban): self;

    public function bankCode(): string;

    public function accountNumber(): string;

    /** @throws MethodNotSupportedException */
    public function branchCode(): string;

    /** @throws MethodNotSupportedException */
    public function checkDigits(): string;

    /** @throws MethodNotSupportedException */
    public function accountType(): string;

    public function __toString(): string;
}
