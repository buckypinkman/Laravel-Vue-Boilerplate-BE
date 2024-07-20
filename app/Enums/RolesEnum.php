<?php
//create enum for role
namespace App\Enums;

enum RolesEnum: string {
    case SUPER_ADMIN = 'super admin';
    case ADMIN = 'admin';
    case ECOM_ADMIN = 'ecommerce_admin';
}
