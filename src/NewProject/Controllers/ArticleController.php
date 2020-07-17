<?php

namespace NewProject\Controllers;

use NewProject\Exceptions\ForbiddenException;
use NewProject\Exceptions\InvalidArgumentException;
use NewProject\Exceptions\NotFoundException;
use NewProject\Exceptions\UnauthorizedException;
use NewProject\Models\Articles\Article;
use NewProject\Models\Users\User;

class ArticleController extends  AbstractController
{
    public function view(int $articleId)
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }

        $this->view->renderHtml('articles/view.php', ['article' => $article]);
    }

    public function edit(int $articleId): void
    {
        $article = Article::getById($articleId);

        if ($article === null) {
            throw new NotFoundException();
        }

        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!$this->user->isAdmin()) {
            throw new ForbiddenException('Только админы могут изменять статьи!!!');
        }

        if (!empty($_POST)) {
            try {
                $article->updateFromArray($_POST);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/edit', ['error' => $e->getMessage(), 'article' => $article]);
                return;
            }
            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }

        $this->view->renderHtml('articles/edit.php', ['article' => $article]);
    }

    public function add(): void
    {
        if ($this->user === null) {
            throw new UnauthorizedException();
        }

        if (!$this->user->isAdmin()) {
            throw new ForbiddenException('Только админы могут добавлять статьи!!!');
        }

        if (!empty($_POST)) {
            try {
                $article = Article::createFromArray($_POST, $this->user);
            } catch (InvalidArgumentException $e) {
                $this->view->renderHtml('articles/add.php', ['error' => $e->getMessage()]);
                return;
            }

            header('Location: /articles/' . $article->getId(), true, 302);
            exit();
        }
        $this->view->renderHtml('articles/add.php');
        return;
    }

    public function delete(int $articleId): void
    {
        $article = Article::getById($articleId);
        if ($article === null) {
            throw new NotFoundException();
        }
        $article->delete();
        var_dump($article);
    }
}
