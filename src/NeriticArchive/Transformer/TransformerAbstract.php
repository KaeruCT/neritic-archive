<?php namespace NeriticArchive\Transformer;

abstract class TransformerAbstract
{
    /**
     * Turn a database record into a generic array
     *
     * @return array
     */
    public abstract function transform($record);

    public function formatDate($timestamp)
    {
        return date('c', $timestamp);
    }
}
