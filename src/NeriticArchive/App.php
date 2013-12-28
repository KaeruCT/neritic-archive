<?php namespace NeriticArchive;

use \NeriticArchive\Transformer\CategoryTransformer;
use \NeriticArchive\Transformer\ForumTransformer;
use \NeriticArchive\Transformer\UserTransformer;
use \NeriticArchive\Transformer\ThreadTransformer;
use \NeriticArchive\Transformer\PostTransformer;
use \NeriticArchive\Db;
use \NeriticArchive\View;

class App
{
    private $db;

    public function __construct(Db $db)
    {
        $this->db = $db;

        $app = new \Slim\Slim();
        $this->app = $app;
        $app->view(new View());
        $app->add(new \JsonApiMiddleware());
        $app->config('debug', false);
        $this->initViews();
    }

    private function get($route, \Closure $fn)
    {
        $app = $this->app;
        $app->get($route, function () use ($app, $fn) {
            $rfn = new \ReflectionFunction($fn);
            $item = $rfn->invokeArgs(func_get_args());
            $app->lastModified(1349222558); // date of the last post ever
            $app->expires('+5 years');

            if ($item === false) {
               $app->render(404, [
                    'error' => true,
                    'msg' => 'Item not found.'
                ]);
            }
            if (is_array($item) && (count($item) === 0)) {
                $app->render(404, [
                    'error' => true,
                    'msg' => 'No items found.'
                ]);
            }

            $app->render(200, ['content' => $item]);
        });
    }

    private function initViews()
    {
        $app = $this->app;
        $db = $this->db;

        $this->get('/categories', function () use ($db, $app)
        {
            return $db->fetchCollection(
                new CategoryTransformer,
                'SELECT * FROM categories ORDER BY ord ASC'
            );
        });

        $this->get('/categories/:id', function ($id) use ($db, $app)
        {
            return $db->fetchItem(
                new CategoryTransformer,
                'SELECT * FROM categories WHERE id = ?', [$id]
            );
        });

        $this->get('/categories/:id/forums', function ($id) use ($db, $app)
        {
            return $db->fetchCollection(
                new ForumTransformer,
                'SELECT * FROM forums WHERE cat = ? ORDER BY ord ASC', [$id]
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
                'SELECT * FROM threads WHERE forum = ? ORDER BY sticky DESC, lastdate DESC', [$id]
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
                 'SELECT p.*, pt.date as ptdate, pt.text text '
                .'FROM `posts` p '
                .'LEFT JOIN `poststext` pt ON p.`id` = pt.`id` '
                .'LEFT JOIN `poststext` pt2 ON pt2.`id` = pt.`id` AND pt2.`revision` = (pt.`revision`+1) '
                .'WHERE p.`thread` = ? AND ISNULL(pt2.`id`) '
                .'GROUP BY p.`id` '
                .'ORDER BY p.`id` ',
                [$id]
            );
        });

        $this->get('/posts/:id', function ($id) use ($db, $app)
        {
            return $db->fetchItem(
                new PostTransformer,
                 'SELECT p.*, pt.date as ptdate, pt.text text '
                .'FROM `posts` p '
                .'LEFT JOIN `poststext` pt ON p.`id` = pt.`id` '
                .'LEFT JOIN `poststext` pt2 ON pt2.`id` = pt.`id` AND pt2.`revision` = (pt.`revision`+1) '
                .'WHERE p.`id` = ? AND ISNULL(pt2.`id`) '
                .'GROUP BY p.`id` '
                .'ORDER BY p.`id` ',
                [$id]
            );
        });

        $this->get('/users', function () use ($db, $app)
        {
            return $db->fetchCollection(
                new UserTransformer,
                'SELECT * FROM users ORDER BY id'
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
