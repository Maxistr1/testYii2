<?php

$roles = require __DIR__ . '/roles.php';
$acl = require __DIR__ . '/acl.php';

return [
    'adminEmail' => 'admin@example.com',

    /**
     * Указиваем группы пользователей в приложении
     */
    'roles' => $roles,

    /**
     * Указиваем доступы к ресурсам для конкретных групп
     */
    'acl' => $acl,
];
