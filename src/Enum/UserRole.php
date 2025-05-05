<?php

namespace App\Enum;

enum UserRole: string
{
    case ORGANISATEUR = 'ROLE_ORGANISATEUR';
    case PARTICIPANT = 'ROLE_PARTICIPANT';
    case JURY = 'ROLE_JURY';
    case COACH = 'ROLE_COACH';

    public function getLabel(): string
    {
        return match($this) {
            self::ORGANISATEUR => 'Rôle Organisateur',
            self::PARTICIPANT => 'Rôle Participant',
            self::JURY => 'Rôle Jury',
            self::COACH => 'Rôle Coach'
        };
    }
}
