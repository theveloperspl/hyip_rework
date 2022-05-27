<?php

namespace App\Http\Requests\Authentication;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * @property string email
 * @property string captcha
 */
class ResendActivationEmailRequest extends FormRequest
{
    use SanitizesInput;

    protected $stopOnFirstFailure = true;

    protected string $route = 'auth.resend';

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
            'email' => ['required', 'min:3', 'email'],
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
            'email' => ['trim', 'escape', 'strip_tags', 'lowercase'],
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
