<?php namespace NeriticArchive\Transformer;

class ForumTransformer extends TransformerAbstract
{
    public function transform($r)
    {
        return [
            'id' => (int)$r['id'],
            'category' => (int)$r['cat'],
            'title' => $r['title'],
            'description' => $r['descr'],
            'threadCount' => (int)$r['threads'],
            'postCount' => (int)$r['posts'],
            'lastPostDate' => $this->formatDate($r['lastdate'])
        ];
    }
}
