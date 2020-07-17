<?php

namespace NewProject\Models\Users;

use NewProject\Exceptions\InvalidArgumentException;
use NewProject\Models\ActiveRecordEntity;

class User extends ActiveRecordEntity
{
    protected $nickname;
    protected $email;
    protected $isConfirmed;
    protected $role;
    protected $passwordHash;
    protected $authToken;
    protected $createdAt;

    public function getNickname(): string
    {
        return $this->nickname;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function isAdmin(): bool
    {
        return ($this->getRole() === 'admin');
    }

    public static function signUp(array $userData): User
    {
        if (empty($userData['nickname'])) {
            throw new InvalidArgumentException('Не передан nickname');
        }

        if (!preg_match('~[a-zA-Z0-9]+~', $userData['nickname'])) {
            throw new InvalidArgumentException('Nickname может состоять только из символов латинского алфавита и цифр');
        }

        if (empty($userData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Email некорректен');
        }

        if(empty($userData['password'])) {
            throw new InvalidArgumentException('Не передан password');
        }

        if (mb_strlen($userData['password']) < 8) {
            throw new InvalidArgumentException('Пароль должен быть не менее 8 символов');
        }

        if (static::findByOneColumn('nickname', $userData['nickname']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким nickname уже существует');
        }

        if (static::findByOneColumn('email', $userData['email']) !== null) {
            throw new InvalidArgumentException('Пользователь с таким email уже существует');
        }

        $user = new User();
        $user->nickname = $userData['nickname'];
        $user->email = $userData['email'];
        $user->passwordHash = password_hash($userData['password'], PASSWORD_DEFAULT);
        $user->isConfirmed = false;
        $user->role = 'user';
        $user->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
        $user->save();

        return $user;
    }

    public function activate(): void
    {
        $this->isConfirmed = true;
        $this->save();
    }

    public static function login(array $loginData): User
    {
        if (empty($loginData['email'])) {
            throw new InvalidArgumentException('Не передан email');
        }

        if (empty($loginData['password'])) {
            throw new InvalidArgumentException('Не передан password');
        }

        $user = User::findByOneColumn('email', $loginData['email']);

        if ($user === null) {
            throw new InvalidArgumentException('Пользователь с таким email не существует!');
        }

        if (!password_verify($loginData['password'], $user->passwordHash)) {
            throw new InvalidArgumentException('Неверный пароль');
        }

        if (!$user->isConfirmed) {
            throw  new InvalidArgumentException('Пользователь не подтвержден');
        }

        $user->refreshAuthToken();
        $user->save();

        return $user;
    }

    public function refreshAuthToken(): void
    {
        $this->authToken = sha1(random_bytes(100)) . sha1(random_bytes(100));
    }

    protected static function getTableName(): string
    {
        return 'users';
    }


}