<?php

namespace App\Enum;

enum ShippingStatusEnum: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in progress';
    case IN_THE_WAY = 'in the way';
    case DELIVERED = 'delivered';
}
