<?php namespace NeriticArchive;

use NeriticArchive\Transformer\TransformerAbstract;
use \PDO;

class Db
{
    private $pdo;

    public function __construct(array $config)
    {
        $this->pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8",
            $config['db_user'],
            $config['db_pass']
        );
    }

    public function fetchItem(TransformerAbstract $t, $query, array $params=[])
    {
        $stmt = $this->query($query, $params);
        return $this->fetchItemStmt($t, $stmt);
    }

    public function fetchCollection(TransformerAbstract $t, $query, array $params=[])
    {
        $stmt = $this->query($query, $params);
        return $this->fetchCollectionStmt($t, $stmt);
    }

    private function query($query, array $params=[])
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    private function fetchCollectionStmt(TransformerAbstract $t, \PDOStatement $stmt)
    {
        $items = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = $t->transform($row);
        }
        return $items;
    }

    private function fetchItemStmt(TransformerAbstract $t, \PDOStatement $stmt)
    {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record === false) {
            return false;
        }
        return $t->transform($record);
    }
}
