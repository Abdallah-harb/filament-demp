<?php

namespace App\Enum;

enum PaymentMethodsEnum: string
{
    case Visa = 'visa';
    case Cash = 'cash';
    case Wallet = 'wallet';
}
