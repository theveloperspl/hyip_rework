<?php

namespace App\Http\Requests\Authentication;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * @property string email
 * @property string token
 * @property string password
 * @property string captcha
 */
class ResetPasswordRequest extends FormRequest
{
    use SanitizesInput;

    protected $stopOnFirstFailure = true;

    protected string $route = 'reset.failed';

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
     * Add route parameters to validation data
     *
     * @param null $keys
     * @return array|null
     */
    public function all($keys = null): ?array
    {
        return array_merge(parent::all(), $this->route()->parameters());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'min:32', 'max:32'],
            'email' => ['required', 'min:3', 'email'],
            'password' => ['required', 'min:8', 'max:32', 'confirmed', 'regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/']
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
            'email' => __('common.wrong_email'),
            'min' => __('common.minChr'),
            'max' => __('common.maxChr'),
            'confirmed' => __('common.passwords_not_equal'),
            'regex' => __('common.strong_password'),
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
            'token' => ['trim', 'escape', 'strip_tags'],
            'email' => ['trim', 'escape', 'strip_tags', 'lowercase'],
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
