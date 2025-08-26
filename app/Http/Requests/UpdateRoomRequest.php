<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'room_number' => 'required|string|max:10|unique:rooms,room_number,' . $this->route('room')->id,
            'type' => 'required|in:single,double,suite,deluxe,presidential',
            'price_per_night' => 'required|numeric|min:0|max:99999.99',
            'status' => 'required|in:available,occupied,maintenance,cleaning',
            'capacity' => 'required|integer|min:1|max:10',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'room_number.required' => 'Room number is required.',
            'room_number.unique' => 'This room number is already taken by another room.',
            'type.required' => 'Room type is required.',
            'type.in' => 'Please select a valid room type.',
            'price_per_night.required' => 'Price per night is required.',
            'price_per_night.numeric' => 'Price must be a valid number.',
            'capacity.required' => 'Room capacity is required.',
            'capacity.min' => 'Room capacity must be at least 1.',
        ];
    }
}