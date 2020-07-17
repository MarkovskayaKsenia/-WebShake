<?php
return [
    '`^$`' => [\NewProject\Controllers\MainController::class, 'main'],
    '~^articles/(\d+)$~' => [\NewProject\Controllers\ArticleController::class, 'view'],
    '~^articles/(\d+)/edit$~' => [\NewProject\Controllers\ArticleController::class, 'edit'],
    '~^articles/add$~' => [\NewProject\Controllers\ArticleController::class, 'add'],
    '~^articles/(\d+)/delete$~' => [\NewProject\Controllers\ArticleController::class, 'delete'],
    '~^users/register$~' => [\NewProject\Controllers\UserController::class, 'signUp'],
    '~^users/(\d+)/activate/(.+)$~' => [\NewProject\Controllers\UserController::class, 'activate'],
    '~^users/login$~' => [\NewProject\Controllers\UserController::class, 'login'],
    '~^users/logout$~' =>[\NewProject\Controllers\UserController::class, 'logout'],
];
