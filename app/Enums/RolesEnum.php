<?php
//create enum for role
namespace App\Enums;

enum RolesEnum: string {
    case SUPER_ADMIN = 'super admin';
    case ADMIN = 'admin';
    case AGEN = 'agen';
    case KADIS = 'kadis';
    case KABAG = 'kabag';
    case KASI = 'kasi';
    case KARAN = 'karan';
    case KAUR = 'kaur';
    case MEMBER = 'member';
}
