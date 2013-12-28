<?php namespace NeriticArchive\Transformer;

class PostTransformer extends TransformerAbstract
{
    public function transform($r)
    {
        return [
            'id' => (int)$r['id'],
            'user' => (int)$r['user'],
            'thread' => $r['thread'],
            'date' => (bool)$r['date'],
            'modifiedDate' => (bool)$r['ptdate'],
            'content' => $r['text'],
        ];
    }
}
