<?php namespace NeriticArchive;

use \NeriticArchive\Transformer\ForumTransformer;
use \NeriticArchive\Transformer\UserTransformer;
use \NeriticArchive\Transformer\ThreadTransformer;
use \NeriticArchive\Transformer\PostTransformer;
use \NeriticArchive\Db;

class App
{
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;

        $app = new \Slim\Slim();
        $this->app = $app;
        $app->view(new \JsonApiView());
        $app->add(new \JsonApiMiddleware());
        $app->config('debug', false);
        $this->initViews();
    }

    private function get($route, \Closure $fn)
    {
        $app = $this->app;
        $app->get($route, function ($id) use ($app, $fn) {
            $item = $fn($id);
            if ($item === false) {
               $app->render(404, ['error' => 'Item not found.']);
            }
            if (is_array($item) && count($item) === 0) {
                $app->render(404, ['error' => 'No items found.']);
            }
            $app->render(200, ['content' => [$item]]);
        });
    }

    private function initViews()
    {
        $app = $this->app;
        $db = $this->db;
        $this->get('/forums', function () use ($db, $app) {
            return $db->fetchCollection(
                new ForumTransformer,
                'SELECT * FROM forums'
            );
        });

        $this->get('/forums/:id', function ($id) use ($db, $app)
        {
            return $db->fetchItem(
                new ForumTransformer,
                'SELECT * FROM forums WHERE id = ?', [$id]
            );
        });

        $this->get('/forums/:id/threads', function ($id) use ($db, $app)
        {
            return $db->fetchCollection(
                new ThreadTransformer,
                'SELECT * FROM threads WHERE forum = ?', [$id]
            );
        });

        $this->get('/threads/:id', function ($id) use ($db, $app)
        {
            return $db->fetchItem(
                new ThreadTransformer,
                'SELECT * FROM threads WHERE id = ?', [$id]
            );
        });

        $this->get('/threads/:id/posts', function ($id) use ($db, $app)
        {
            return $db->fetchCollection(
                new PostTransformer,
                ' SELECT p.*, pt.date as ptdate, pt.text text '
                .'FROM `posts` p '
                .'LEFT JOIN `poststext` pt ON p.`id` = pt.`id` '
                .'LEFT JOIN `poststext` pt2 ON pt2.`id` = pt.`id` AND pt2.`revision` = (pt.`revision`+1) '
                .'WHERE p.`thread` = ? AND ISNULL(pt2.`id`) '
                .'GROUP BY p.`id` '
                .'ORDER BY p.`id` ',
                [$id]
            );
        });

        $this->get('/users', function () use ($db, $app) {
            return $db->fetchCollection(
                new UserTransformer,
                'SELECT * FROM users'
            );
        });

        $this->get('/users/:id', function ($id) use ($db, $app)
        {
            return $db->fetchItem(
                new UserTransformer,
                'SELECT * FROM users WHERE id = ?', [$id]
            );
        });
    }

    public function run()
    {
        $this->app->run();
    }
}
