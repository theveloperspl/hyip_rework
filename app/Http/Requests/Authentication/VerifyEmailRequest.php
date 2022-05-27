<?php

namespace App\Http\Requests\Authentication;

use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * @property string token
 * @property string email
 */
class VerifyEmailRequest extends FormRequest
{
    use SanitizesInput;

    protected $stopOnFirstFailure = true;

    protected string $route = 'verify.failed';

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
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
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
