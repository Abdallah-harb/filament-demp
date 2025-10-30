<?php

namespace App\Enum;

enum ProductStatusEnum: string
{
    case IN_STOCK = 'in_stock';
    case SOLID_OUT = 'sold_out';
    case COMING_SOON = 'coming_soon';
}
