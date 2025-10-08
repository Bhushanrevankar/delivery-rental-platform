<?php

namespace App\Models\Constants;

class WalletRequestStatus
{
    public const pending = 'pending';

    public const approved = 'approved';

    public const rejected = 'rejected';

    public const canceled = 'canceled';

    public const values = [
        self::pending,
        self::approved,
        self::rejected,
        self::canceled,
    ];
}
