<?php
declare(strict_types=1);

final class EntityMap
{
    private const MAP = [
        'aviso' => [
            'columns' => [
                'col_1','col_2','col_3','col_4','col_5','col_6','col_7','col_8'
            ],
        ],
    ];

    public static function has(string $entity): bool
    {
        return isset(self::MAP[$entity]);
    }

    public static function getColumns(string $entity): array
    {
        return self::MAP[$entity]['columns'] ?? [];
    }

    public static function expectedColumnCount(string $entity): int
    {
        $cols = self::getColumns($entity);
        return count($cols);
    }
}