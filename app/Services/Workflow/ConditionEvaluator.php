<?php

namespace App\Services\Workflow;

use Illuminate\Database\Eloquent\Model;

class ConditionEvaluator
{
    /**
     * Evaluate a condition array against a model or data payload
     */
    public static function evaluate(array $condition, Model|array $data): bool
    {
        $field = $condition['field'] ?? null;
        $operator = $condition['operator'] ?? '==';
        $targetValue = $condition['value'] ?? null;

        if (!$field) {
            return true;
        }

        $actualValue = is_array($data) ? ($data[$field] ?? null) : ($data->{$field} ?? null);

        return match ($operator) {
            '==', 'eq' => $actualValue == $targetValue,
            '!=', 'neq' => $actualValue != $targetValue,
            '>', 'gt' => (float)$actualValue > (float)$targetValue,
            '>=', 'gte' => (float)$actualValue >= (float)$targetValue,
            '<', 'lt' => (float)$actualValue < (float)$targetValue,
            '<=', 'lte' => (float)$actualValue <= (float)$targetValue,
            'contains' => str_contains(strtolower((string)$actualValue), strtolower((string)$targetValue)),
            'in' => is_array($targetValue) && in_array($actualValue, $targetValue),
            'not_empty' => !empty($actualValue),
            default => true,
        };
    }
}
