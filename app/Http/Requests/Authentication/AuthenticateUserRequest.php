<?php

namespace App\Http\Requests\Authentication;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * @property string username
 * @property string password
 * @property string captcha
 */
class AuthenticateUserRequest extends FormRequest
{
    use SanitizesInput;

    protected $stopOnFirstFailure = true;

    protected string $route = 'auth.login';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => 'required | min:3 | string',
            'password' => ['required', 'min:8', 'max:32', 'regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/'],
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'required' => __('common.required'),
            'min' => __('common.minChr'),
            'max' => __('common.maxChr'),
            'regex' => __('common.strong_password')
        ];
    }

    /**
     * Add additional fields filters and tweaks
     *
     * @return array
     */
    public function filters(): array
    {
        return [
            'username' => ['trim', 'escape', 'strip_tags'],
            'password' => ['trim', 'escape', 'strip_tags']
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $key = $validator->errors()->keys();
        $key = $key[0];
        $value = $validator->errors()->first();

        if ($this->wantsJson()) {
            $response = response()->json(['type' => 'error', 'field' => $key, 'message' => $value], 200);
        } else {
            $response = redirect()->route($this->route)->withErrors($validator);
        }

        throw new ValidationException($validator, $response);
    }
}
