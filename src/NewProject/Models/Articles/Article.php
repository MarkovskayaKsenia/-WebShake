<?php
namespace NewProject\Models\Articles;

use NewProject\Exceptions\ForbiddenException;
use NewProject\Exceptions\InvalidArgumentException;
use NewProject\Models\ActiveRecordEntity;
use NewProject\Models\Users\User;

class Article extends ActiveRecordEntity
{
    protected $name;
    protected $text;
    protected $authorId;
    protected $createdAt;

    public function getName(): string
    {
        return $this->name;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getAuthor(): User
    {
        return User::getById($this->authorId);
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function setAuthor(User $author): void
    {
        $this->authorId = $author->getId();
    }

    public static function createFromArray(array $fields, User $author)
    {
         if (empty($fields['name'])) {
             throw new InvalidArgumentException('Не передано поле name');
         }

         if (empty($fields['text'])) {
             throw new InvalidArgumentException('Не передано поле text');
         }
         $article = new Article();
         $article->setAuthor($author);
         $article->setName($fields['name']);
         $article->setText($fields['text']);

         $article->save();

         return $article;
    }

    public function updateFromArray(array $fields): Article
    {
        if (empty($fields['name'])) {
            throw new InvalidArgumentException('Не передано поле name');
        }

        if (empty($fields['text'])) {
            throw new InvalidArgumentException('Не передано поле text');
        }

        $this->setName($fields['name']);
        $this->setText($fields['text']);
        $this->save();

        return $this;
    }

    protected static function getTableName(): string
    {
        return 'articles';
    }

}
