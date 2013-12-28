<?php namespace NeriticArchive\Transformer;

class CategoryTransformer extends TransformerAbstract
{
    public function transform($r)
    {
        return [
            'id' => (int)$r['id'],
            'title' => $r['title']
        ];
    }
}
