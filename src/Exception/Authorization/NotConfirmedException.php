<?php

declare(strict_types=1);

namespace App\Exception\Authorization;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

final class NotConfirmedException extends AuthenticationException
{
    #[\Override]
    public function getMessageKey(): string
    {
        return 'Account has not been confirmed.';
    }
}
