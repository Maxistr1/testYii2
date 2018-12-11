<?php

/**
 * Указиваем настройку типов доступа open или close, которые определяют действие для
 * ресурсов которых нет в $acl.
 * close - все неуказанные ресурсы в $acl запрещаются, а указанные разрешаются для
 *         конкретной группы.
 * open - все неуказанные ресурсы в $acl разрешаются, а указанные запрещаются для
 *        конкретной группы.
 */

return [
    'admin' => [
        'groupAclType' => 'open',
        'resources' => [
//            'MainController' => ['actionIndex']
        ]
    ],

    'user' => [
        'groupAclType' => 'open',
        'resources' => [
            'MainController' => [
                'actionSetdata',
            ],
        ]
    ],

    'guest' => [
        'groupAclType' => 'close',
        'resources' => [
            'MainController' => [
                'actionIndex',
                'actionLogin',
                'actionLogout'
            ],
        ]
    ]
];