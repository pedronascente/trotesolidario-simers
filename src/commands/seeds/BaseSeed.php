<?php

namespace app\commands\seeds;

use Yii;

abstract class BaseSeed
{
    abstract public static function seedName();

    abstract public function run();

    /**
     * @param string $table
     * @param array<int, array<string, mixed>> $rows
     */
    protected function upsertRows($table, array $rows)
    {
        foreach ($rows as $row) {
            Yii::$app->db->createCommand()->upsert($table, $row, $row)->execute();
        }
    }

    protected function now()
    {
        return date('Y-m-d H:i:s');
    }
}
