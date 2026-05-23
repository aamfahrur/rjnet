<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('edit customer') ?? false;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'string|max:200',
            'email'     => 'nullable|email|max:100|unique:customers,email,' . $this->route('customer')->id,
            'phone'     => 'string|max:20|unique:customers,phone,' . $this->route('customer')->id,
            'phone_alt' => 'nullable|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'notes'     => 'nullable|string|max:2000',
        ];
    }
}
