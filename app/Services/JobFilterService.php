<?php

namespace App\Services;

use App\Models\Job;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class JobFilterService
{
    /**
     * Main filter method that processes the request and returns filtered jobs
     *
     * @param array $params Request parameters containing 'filter' key
     * @param array $with Relationships to eager load
     * @param int|null $perPage Pagination items per page (null returns all)
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \InvalidArgumentException|\Exception
     *
     * @example
     * $service->filter(['filter' => 'salary_min>50000'], ['languages'], 10)
     */

    public function filter(array $params, array $with = [], int $perPage = null)
    {
        try {
            $query = Job::query();

            if (isset($params['filter'])) {
                $parsedConditions = $this->parseFilterQuery($params['filter']);
                $query->where(function ($q) use ($parsedConditions) {
                    $this->applyConditionsToQuery($q, $parsedConditions);
                });
            }

            if (!empty($with)) {
                $query->with($with);
            }

            return $perPage ? $query->paginate($perPage) : $query->get();

        } catch (\Exception $e) {
            Log::error('Job filter error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Parse the filter query string into an abstract syntax tree (AST)
     *
     * @param string $query Filter string from request
     * @return array Parsed conditions array
     *
     * @throws \InvalidArgumentException
     */
    private function parseFilterQuery(string $query): array
    {
        $tokens = $this->tokenizeFilterQuery($query);
        return $this->parseTokens($tokens);
    }

    /**
     * Convert filter string into lexical tokens
     *
     * @param string $query Input filter string
     * @return array Array of token structures
     *
     * @throws \InvalidArgumentException
     *
     * @example
     * Input:  "salary_min>50000 AND (locations IS_ANY(Remote))"
     * Output: Array of tokens with type/value pairs
     */
    private function tokenizeFilterQuery(string $query): array
    {
        $tokens = [];
        $length = strlen($query);
        $currentPos = 0;

        while ($currentPos < $length) {
            $char = $query[$currentPos];

            if (ctype_space($char)) {
                $currentPos++;
                continue;
            }

            if ($char === '(' || $char === ')') {
                $tokens[] = ['type' => 'paren', 'value' => $char];
                $currentPos++;
                continue;
            }

            if (preg_match('/^(AND|OR)/i', substr($query, $currentPos), $matches)) {
                $tokens[] = ['type' => 'logical', 'value' => strtoupper($matches[1])];
                $currentPos += strlen($matches[1]);
                continue;
            }

            if (preg_match(
                '/^([\w:]+)\s*(=|!=|>=|<=|>|<|HAS_ANY|IS_ANY|LIKE)\s*((?:\([^)]+\)|"[^"]+"|\'[^\']+\'|\S+))/',
                substr($query, $currentPos),
                $matches
            )) {
                $rawValue = $matches[3];
                $value = trim($rawValue, "'\"()");

                if (str_starts_with($rawValue, '(')) {
                    $value = array_map('trim', explode(',', $value));
                }

                $tokens[] = [
                    'type' => 'condition',
                    'field' => $matches[1],
                    'operator' => strtoupper($matches[2]),
                    'value' => $value,
                    'raw' => $matches[0]
                ];

                $currentPos += strlen($matches[0]);
                continue;
            }

            throw new InvalidArgumentException("Unexpected character at position $currentPos: '$char'");
        }

        return $tokens;
    }

    /**
     * Convert tokens into nested condition structure (AST)
     *
     * @param array $tokens Token array from tokenizeFilterQuery
     * @param int &$index Current token index (used recursively)
     * @return array Structured conditions array
     *
     * @throws \InvalidArgumentException
     */
    private function parseTokens(array $tokens, int &$index = 0): array
    {
        $conditions = [];
        $currentLogical = 'AND';

        while ($index < count($tokens)) {
            $token = $tokens[$index];

            if ($token['type'] === 'paren' && $token['value'] === '(') {
                $index++;
                $groupConditions = $this->parseTokens($tokens, $index);

                if ($index < count($tokens) && $tokens[$index]['type'] === 'paren' && $tokens[$index]['value'] === ')') {
                    $index++;
                }

                $conditions[] = [
                    'type' => 'group',
                    'conditions' => $groupConditions,
                    'logical' => $currentLogical
                ];
                $currentLogical = 'AND';
            } elseif ($token['type'] === 'logical') {
                $currentLogical = $token['value'];
                $index++;
            } elseif ($token['type'] === 'condition') {
                $conditions[] = [
                    'type' => 'condition',
                    'field' => $token['field'],
                    'operator' => $token['operator'],
                    'value' => $token['value'],
                    'logical' => $currentLogical
                ];
                $currentLogical = 'AND';
                $index++;
            } elseif ($token['type'] === 'paren' && $token['value'] === ')') {
                break;
            } else {
                throw new InvalidArgumentException("Unexpected token: " . json_encode($token));
            }
        }

        return $conditions;
    }

    /**
     * Recursively apply conditions to Eloquent query builder
     *
     * @param Builder $query Query builder instance
     * @param array $conditions Parsed conditions structure
     * @param string $logical Default logical operator (AND/OR)
     */
    private function applyConditionsToQuery(Builder $query, array $conditions, string $logical = 'AND'): void
    {
        foreach ($conditions as $condition) {
            $method = $condition['logical'] ?? $logical;

            if ($condition['type'] === 'group') {
                $query->where(function ($subQuery) use ($condition) {
                    $this->applyConditionsToQuery($subQuery, $condition['conditions']);
                }, null, null, $method);
            } else {
                $this->applyCondition($query, $condition, $method);
            }
        }
    }

    /**
     * Apply individual condition to query builder
     *
     * @param Builder $query Query builder instance
     * @param array $condition Single condition structure
     * @param string $logical Logical operator (AND/OR)
     *
     * @throws \InvalidArgumentException
     */
    private function applyCondition(Builder $query, array $condition, string $logical = 'AND'): void
    {
        $method = $logical === 'OR' ? 'orWhere' : 'where';
        $field = $condition['field'];
        $operator = $condition['operator'];
        $value = $condition['value'];

        try {
            switch (true) {
                case $field === 'languages' && $operator === 'HAS_ANY':
                    $this->applyLanguagesCondition($query, $method, $value);
                    break;

                case $field === 'categories' && $operator === 'HAS_ANY':
                    $this->applyCategoriesCondition($query, $method, $value);
                    break;

                case $field === 'locations' && $operator === 'IS_ANY':
                    $this->applyLocationsCondition($query, $method, $value);
                    break;

                case str_contains($field, ':'):
                    $this->applyEavCondition($query, $method, $field, $operator, $value);
                    break;

                default:
                    $this->applyStandardCondition($query, $method, $field, $operator, $value);
            }
        } catch (\Exception $e) {
            Log::error("Condition application failed", [
                'field' => $field,
                'operator' => $operator,
                'value' => $value,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Handle languages relationship filter (HAS_ANY)
     *
     * @param Builder $query Query builder
     * @param string $method Where/orWhere method
     * @param mixed $value Array or single value
     */
    private function applyLanguagesCondition(Builder $query, string $method, $value): void
    {
        $query->{$method . 'Has'}('languages', function ($q) use ($value) {
            $q->whereIn('name', (array)$value);
        });
    }

    /**
     * Handle locations filter with special Remote handling
     *
     * @param Builder $query Query builder
     * @param string $method Where/orWhere method
     * @param mixed $value Location criteria
     *
     * @example
     * IS_ANY(Remote,New York) converts to:
     * WHERE (is_remote = 1 OR city LIKE '%New York%')
     */
    private function applyLocationsCondition(Builder $query, string $method, $value): void
    {
        $query->{$method . 'Has'}('locations', function ($q) use ($value) {
            $q->where(function ($subQuery) use ($value) {
                foreach ((array)$value as $val) {
                    $val = trim($val);
                    if (strtolower($val) === 'remote') {
                        $subQuery->orWhere('is_remote', true);
                    } else {
                        $subQuery->orWhere('city', 'LIKE', "%$val%")
                            ->orWhere('state', 'LIKE', "%$val%")
                            ->orWhere('country', 'LIKE', "%$val%");
                    }
                }
            });
        });
    }

    /**
     * Handle EAV attributes through attributeValues relationship
     *
     * @param Builder $query Query builder
     * @param string $method Where/orWhere method
     * @param string $field Field in format "attribute:name"
     * @param string $operator Comparison operator
     * @param mixed $value Filter value
     */
    private function applyEavCondition(Builder $query, string $method, string $field, string $operator, $value): void
    {
        [$relation, $attribute] = explode(':', $field);

        $query->{$method . 'Has'}('attributeValues', function ($q) use ($attribute, $operator, $value) {
            $q->whereHas('attribute', function ($subQuery) use ($attribute) {
                $subQuery->where('name', $attribute);
            })->where(function ($subQuery) use ($operator, $value) {
                $this->applyValueCondition($subQuery, $operator, $value);
            });
        });
    }


    /**
     * Handle standard field comparisons
     *
     * @param Builder $query Query builder
     * @param string $method Where/orWhere method
     * @param string $field Database column
     * @param string $operator Comparison operator
     * @param mixed $value Filter value
     */
    private function applyStandardCondition(Builder $query, string $method, string $field, string $operator, $value): void
    {
        if (is_array($value) && in_array($operator, ['HAS_ANY', 'IS_ANY'])) {
            $query->{$method . 'In'}($field, $value);
        } else {
            $query->{$method}($field, $this->normalizeOperator($operator), $value);
        }
    }


    private function applyValueCondition(Builder $query, string $operator, $value): void
    {
        if (in_array($operator, ['>', '<', '>=', '<='])) {
            if (!is_numeric($value)) {
                throw new InvalidArgumentException("Numeric operator used with non-numeric value: $value");
            }
            $query->whereRaw("CAST(value AS DECIMAL(10,2)) $operator ?", [$value]);
        } else {
            $query->where('value', $this->normalizeOperator($operator), $value);
        }
    }
    /**
     * Handle category relationships filter (HAS_ANY)
     *
     * @param Builder $query Query builder
     * @param string $method Where/orWhere method
     * @param mixed $value Array or single value
     */

    private function applyCategoriesCondition(Builder $query, string $method, $value): void
    {
        $query->{$method . 'Has'}('categories', function ($q) use ($value) {
            $q->whereIn('name', (array)$value);
        });
    }


    /**
     * Normalize operator symbols for database compatibility
     *
     * @param string $operator Input operator
     * @return string Normalized operator
     */
    private function normalizeOperator(string $operator): string
    {
        return match ($operator) {
            '!=' => '<>',
            'HAS_ANY', 'IS_ANY' => '=',
            default => $operator,
        };
    }
}




// Complex Query
//GET /api/jobs?filter=(job_type=full-time AND
//    (languages HAS_ANY (PHP,JavaScript))) AND (locations IS_ANY
//(New York,Remote)) AND attribute:years_experience>=3


//The code processes this as:
// First Step
//[
//    ['type' => 'paren', 'value' => '('],
//    ['type' => 'condition', 'field' => 'job_type', ...],
//    ['type' => 'logical', 'value' => 'AND'],
//    // ... other tokens
//]

//Second Step -->
//[
//    'type' => 'group',
//    'conditions' => [
//        ['field' => 'job_type', 'operator' => '=', ...],
//        [
//            'type' => 'group',
//            'conditions' => [/* languages condition */],
//            'logical' => 'AND'
//        ]
//    ],
//    'logical' => 'AND'
//]

// Final Step

//WHERE (
//    job_type = 'full-time' AND
//    EXISTS (/* languages subquery */)
//) AND (
//EXISTS (/* locations subquery */)
//) AND (
//EXISTS (/* attribute values subquery */)
//)
