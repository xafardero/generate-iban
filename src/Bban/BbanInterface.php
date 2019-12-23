<?php

declare(strict_types=1);

namespace IbanGenerator\Bban;

use InvalidArgumentException;

interface BbanInterface
{
    /** @throws InvalidArgumentException */
    public static function fromString(string $bban): BbanInterface;

    public function bankCode(): string;

    public function branchCode(): string;

    public function checkDigits(): string;

    public function accountNumber(): string;

    public function __toString(): string;
}
