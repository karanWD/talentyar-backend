<?php

namespace App\Http\Requests;

use App\Models\ActivityField;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class ActivityFieldSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'answers' => [$this->answersPresenceRule(), 'array'],
        ];

        $activityField = $this->activityField();

        if (! $activityField) {
            return $rules;
        }

        foreach ($activityField->specialtyQuestions() as $question) {
            $questionKey = $question['key'];
            $questionRules = $this->rulesForQuestion($question);

            if ($questionRules !== []) {
                $rules["answers.{$questionKey}"] = $questionRules;
            }

            if (($question['type'] ?? null) === 'multi_select') {
                $optionKeys = array_keys($question['options'] ?? []);
                $rules["answers.{$questionKey}.*"] = ['string', 'distinct', Rule::in($optionKeys)];
            }
        }

        return $rules;
    }

    public function withValidator(Validator $validator): void
    {
        $activityField = $this->activityField();

        if (! $activityField) {
            return;
        }

        $validator->after(function (Validator $validator) use ($activityField) {
            $answers = $this->input('answers');

            if (! is_array($answers)) {
                return;
            }

            $allowedQuestionKeys = $activityField->specialtyQuestionKeys();

            foreach (array_keys($answers) as $questionKey) {
                if (! in_array($questionKey, $allowedQuestionKeys, true)) {
                    $validator->errors()->add(
                        "answers.{$questionKey}",
                        "The {$questionKey} field is not defined for this activity field."
                    );
                }
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function validatedAnswers(): array
    {
        $answers = $this->validated('answers', []);

        return is_array($answers) ? $answers : [];
    }

    protected function activityField(): ?ActivityField
    {
        $activityField = $this->route('activityField');

        return $activityField instanceof ActivityField ? $activityField : null;
    }

    protected function answersPresenceRule(): string
    {
        return $this->isPartial() ? 'sometimes' : 'required';
    }

    /**
     * @param array<string, mixed> $question
     * @return array<int, mixed>
     */
    protected function rulesForQuestion(array $question): array
    {
        $type = $question['type'] ?? null;
        $required = (bool) ($question['required'] ?? false);
        $rules = [$this->questionPresenceRule($required)];

        if (! $required) {
            $rules[] = 'nullable';
        }

        if ($type === 'single_select') {
            $rules[] = 'string';
            $rules[] = Rule::in(array_keys($question['options'] ?? []));

            return $rules;
        }

        if ($type === 'multi_select') {
            $rules[] = 'array';

            if ($required && ! $this->isPartial()) {
                $rules[] = 'min:1';
            }

            return $rules;
        }

        $validationRules = $question['validation'] ?? [];

        if (! is_array($validationRules) || $validationRules === []) {
            $validationRules = match ($type) {
                'number' => ['numeric'],
                'text' => ['string'],
                default => ['string'],
            };
        }

        return array_merge($rules, $validationRules);
    }

    protected function questionPresenceRule(bool $required): string
    {
        if ($this->isPartial()) {
            return 'sometimes';
        }

        return $required ? 'required' : 'sometimes';
    }

    abstract protected function isPartial(): bool;
}
