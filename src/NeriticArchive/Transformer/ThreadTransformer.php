<?php namespace NeriticArchive\Transformer;

class ThreadTransformer extends TransformerAbstract
{
    public function transform($r)
    {
        return [
            'id' => (int)$r['id'],
            'title' => $r['title'],
            'user' => (int)$r['user'],
            'closed' => (bool)$r['closed'],
            'sticky' => (bool)$r['sticky'],
            'postCount' => (int)$r['replies'],
            'viewCount' => (int)$r['views'],
            'lastPostDate' => $this->formatDate($r['lastdate']),
            'lastPostUser' => (int)$r['lastuser']
        ];
    }
}
