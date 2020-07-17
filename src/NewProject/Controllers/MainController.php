<?php

namespace NewProject\Controllers;

use NewProject\Models\Articles\Article;

class MainController extends AbstractController
{
    public function main()
    {
        $articles = Article::findAll();

        $this->view->renderHtml('main/main.php', ['articles' => $articles]);
    }
}
