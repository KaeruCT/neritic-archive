<?php namespace NeriticArchive\Transformer;

class CommentTransformer extends TransformerAbstract
{
    public function transform($r)
    {
        return [
            'id' => (int)$r['id'],
            'userTo' => (int)$r['userto'],
            'userFrom' => (int)$r['userfrom'],
            'date' => $this->formatDate($r['date']),
            'content' => $r['text']
        ];
    }
}
