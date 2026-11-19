<?php

declare(strict_types=1);

namespace App\Core;

final class Validator
{

    private array $errors = [];

    public function __construct(private array $data)
    {
    }

    public function required(string $field, string $label): self
    {
        if ($this->value($field) === '') {
            $this->errors[$field] ??= $label . ' is required.';
        }

        return $this;
    }

    public function email(string $field, string $label): self
    {
        $value = $this->value($field);

        if ($value !== '' && ! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $label . ' must be a valid email address.';
        }

        return $this;
    }

    public function minLength(string $field, int $length, string $label): self
    {
        $value = $this->value($field);

        if ($value !== '' && mb_strlen($value) < $length) {
            $this->errors[$field] = sprintf('%s must be at least %d characters.', $label, $length);
        }

        return $this;
    }

    public function maxLength(string $field, int $length, string $label): self
    {
        $value = $this->value($field);

        if (mb_strlen($value) > $length) {
            $this->errors[$field] = sprintf('%s may not be longer than %d characters.', $label, $length);
        }

        return $this;
    }

    public function matches(string $field, string $otherField, string $label, string $otherLabel): self
    {
        if ($this->value($field) !== $this->value($otherField)) {
            $this->errors[$field] = $label . ' must match ' . $otherLabel . '.';
        }

        return $this;
    }

    public function integer(string $field, string $label): self
    {
        $value = $this->value($field);

        if ($value !== '' && filter_var($value, FILTER_VALIDATE_INT) === false) {
            $this->errors[$field] = $label . ' must be a whole number.';
        }

        return $this;
    }

    public function decimal(string $field, string $label): self
    {
        $value = $this->value($field);

        if ($value === '' || ! is_numeric($value) || (float) $value < 0) {
            $this->errors[$field] = $label . ' must be a number of zero or more.';
        }

        return $this;
    }

    public function atLeast(string $field, float $minimum, string $label): self
    {
        $value = $this->value($field);

        if ($value === '' || ! is_numeric($value) || (float) $value < $minimum) {
            $this->errors[$field] = sprintf('%s must be at least %s.', $label, format_number($minimum));
        }

        return $this;
    }

    public function add(string $field, string $message): self
    {
        $this->errors[$field] ??= $message;

        return $this;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return $this->errors === [];
    }

    public function errors(): array
    {
        return $this->errors;
    }

    private function value(string $field): string
    {
        $value = $this->data[$field] ?? '';

        return is_string($value) ? trim($value) : '';
    }
}
