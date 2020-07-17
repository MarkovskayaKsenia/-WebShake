<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Мой блог</title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body>

<table class="layout">
    <tr>
        <td colspan="2" class="header">
            <a href="/">Мой блог</a>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right">
            <?php if (!empty($user)): ?>
            <div><?= 'Привет, ' . $user->getNickname() ?> | <a href="/users/logout">Выйти</a></div>
            <?php else: ?>
            <div><a href="/users/login">Войти</a> | <a href="/users/register">Зарегистрироваться</a></div>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <td>
