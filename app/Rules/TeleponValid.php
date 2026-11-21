<?php

namespace App\Rules;

use App\Support\Telepon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TeleponValid implements ValidationRule
{

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Telepon::valid(((string) $value))) {
            $fail('nomor telepon tidak valid: hanya angka, diawali 08, dan minimal 10 digit');
        }
    }
}
