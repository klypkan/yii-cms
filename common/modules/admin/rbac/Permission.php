<?php
namespace common\modules\admin\rbac;

class Permission
{
    const ACCESS_TO_ADMIN_DASHBOARD  = 'Access to admin dashboard';
    const MANAGE_USERS = 'Manage users';
    const MANAGE_ROLES = 'Manage roles';
    const MANAGE_SITES = 'Manage sites';
    const MANAGE_PAGES = 'Manage pages';
    const MANAGE_POSTS = 'Manage posts';
    const MANAGE_MENUS = 'Manage menus';
    const UPLOAD = 'Upload';
    const READ_LOG = 'Read log';
    const IMPORT_DATA = 'Import data';
    const EXPORT_DATA = 'Export data';

    static function getConstants() {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}