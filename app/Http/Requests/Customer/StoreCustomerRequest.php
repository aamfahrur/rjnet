<?php

declare(strict_types=1);

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create customer') ?? false;
    }

    public function rules(): array
    {
        return [
            'full_name'           => 'required|string|max:200',
            'email'               => 'nullable|email|max:100|unique:customers,email',
            'phone'               => 'required|string|max:20|unique:customers,phone',
            'phone_alt'           => 'nullable|string|max:20',
            'id_number'           => 'nullable|string|max:50',
            'registration_date'   => 'nullable|date',
            'notes'               => 'nullable|string|max:2000',
            'address.label'       => 'nullable|string|max:50',
            'address.address'     => 'required|string|max:500',
            'address.village'     => 'nullable|string|max:100',
            'address.district'    => 'nullable|string|max:100',
            'address.city'        => 'nullable|string|max:100',
            'address.province'    => 'nullable|string|max:100',
            'address.postal_code' => 'nullable|string|max:10',
            'address.latitude'    => 'nullable|numeric',
            'address.longitude'   => 'nullable|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'       => 'Nama lengkap wajib diisi.',
            'phone.required'           => 'Nomor telepon wajib diisi.',
            'phone.unique'             => 'Nomor telepon sudah terdaftar.',
            'address.address.required' => 'Alamat wajib diisi.',
        ];
    }
}
