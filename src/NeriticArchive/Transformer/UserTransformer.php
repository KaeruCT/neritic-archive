<?php namespace NeriticArchive\Transformer;

class UserTransformer extends TransformerAbstract
{
    private static $POWER_LEVELS = [
       -1 => 'Banned',
        0 => 'User',
        1 => 'Local Moderator',
        2 => 'Global Moderator',
        3 => 'Administrator'
    ];

    public function transform($r)
    {
        return [
            'id' => (int)$r['id'],
            'name' => $r['name'],
            'realName' => $r['realname'],
            'location' => $r['location'],
            'email' => $r['email'],
            'website' => $r['homeurl'],
            'biography' => $r['bio'],
            'color' => $r['color'],
            'title' => $r['title'],
            'threadCount' => (int)$r['threads'],
            'postCount' => (int)$r['posts'],
            'totalTime' => (int)$r['totaltime'],
            'registrationDate' => $this->formatDate($r['regdate']),
            'lastPostDate' => $this->formatDate($r['lastpost']),
            'lastViewDate' => $this->formatDate($r['lastview']),
            'powerLevel' => $this->formatPowerLevel($r['power']),
        ];
    }

    private function formatPowerLevel($level)
    {
        $level = (int)$level;

        if (array_key_exists($level, self::$POWER_LEVELS)) {
            return self::$POWER_LEVELS[$level];
        }

        return 'Unknown';
    }
}
