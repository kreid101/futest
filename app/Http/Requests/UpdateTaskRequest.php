<?php

namespace App\Http\Requests;

class UpdateTaskRequest extends StoreTaskRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        foreach ($rules as $field => &$fieldRules) {
            if ($this->isMethod('PATCH')) {
                array_unshift($fieldRules, 'sometimes');
            } elseif (in_array($field, ['status', 'priority'], true)) {
                $fieldRules = array_values(array_filter($fieldRules, fn ($rule): bool => $rule !== 'sometimes'));
            }
        }

        return $rules;
    }
}
