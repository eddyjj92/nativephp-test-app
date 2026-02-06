<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class TransportationCostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>|string>
     */
    public function rules(): array
    {
        return [
            'cost_ring_id' => ['required', 'integer', 'min:1'],
            'weight_kg' => ['required', 'numeric', 'gt:0'],
            'total_cost' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
