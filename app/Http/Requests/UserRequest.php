<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee') ? $this->route('employee')->id : null;
        return [
            'name' => 'required|min:3',
            'email' => 'required|email' . ($employeeId ? '|unique:employees,email,' . $employeeId : '|unique:employees,email'), // Adjust the uniqueness rule
            'position' => 'required|min:3'
        ];
    }    

    public function messages(): array
    {
        return [
            'name.required' => 'Please Enter Name',
            'name.min' => 'Nmae must have Minimum 3 charater',

            'email.required' => 'Please Enter Email.',
            'email.email' => 'Please Enter correct E-mail.',
            'email.unique' => 'This E-mail has already been taken.',

            'position.required' => 'Please Enter Your Current Employment Position',
            'position.min' => 'Position name must have Minimum 3 charater',

        ];
    }
}
