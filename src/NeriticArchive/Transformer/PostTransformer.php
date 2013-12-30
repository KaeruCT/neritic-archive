<?php namespace NeriticArchive\Transformer;

class PostTransformer extends TransformerAbstract
{
    public function transform($r)
    {
        return [
            'id' => (int)$r['id'],
            'user' => (int)$r['user'],
            'thread' => $r['thread'],
            'date' => $this->formatDate($r['date']),
            'modifiedDate' => $this->formatDate($r['ptdate']),
            'content' => $r['text'],
        ];
    }
}
