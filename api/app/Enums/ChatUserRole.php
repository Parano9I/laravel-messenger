<?php

namespace App\Enums;

enum ChatUserRole: string
{
    case OWNER = 'owner';

    case ADMIN = 'admin';

    case COMMON = 'common';
}
