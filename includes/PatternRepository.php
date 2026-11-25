<?php

declare(strict_types=1);

namespace App\Includes;

use PDO;

final class PatternRepository
{
    public const FACETS = [
        'strategies' => 'Privacy strategy',
        'iso_phase' => 'Lifecycle phase',
        'collocazione_mvc' => 'MVC layer',
    ];

    public function __construct(private PDO $db)
    {
    }

    public function search(string $query, array $filters = []): array
    {
        $conditions = [];
        $parameters = [];
        if ($query !== '') {
            $columns = ['name', 'strategies', 'description', 'context', 'gdpr_article', 'cwe_categories'];
            $conditions[] = '(' . implode(' OR ', array_map(
                static fn ($column) => "$column LIKE ? ESCAPE '!'", $columns
            )) . ')';
            $parameters = array_fill(0, count($columns), self::contains($query));
        }

        foreach (self::FACETS + ['name' => 'Name', 'gdpr_article' => 'GDPR article'] as $column => $label) {
            $value = $filters[$column] ?? '';
            if ($value === '') {
                continue;
            }
            $isFragment = $column === 'gdpr_article' || $column === 'name';
            $conditions[] = $isFragment ? "$column LIKE ? ESCAPE '!'" : "$column = ?";
            $parameters[] = $isFragment ? self::contains($value) : $value;
        }

        $sql = 'SELECT id, name, strategies, description, context, iso_phase, gdpr_article, collocazione_mvc FROM patterns';
        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $statement = $this->db->prepare($sql . ' ORDER BY name, id LIMIT 100');
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function facets(): array
    {
        $options = [];
        foreach (self::FACETS as $column => $label) {
            $query = $this->db->query("SELECT DISTINCT $column FROM patterns WHERE $column <> '' ORDER BY $column");
            $options[$column] = $query->fetchAll(PDO::FETCH_COLUMN);
        }
        return $options;
    }

    private static function contains(string $value): string
    {
        return '%' . strtr($value, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
    }
}
