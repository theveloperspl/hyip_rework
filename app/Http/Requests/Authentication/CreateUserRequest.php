<?php

namespace App\Http\Requests\Authentication;

use App\Rules\EmailTypo;
use Elegant\Sanitizer\Laravel\SanitizesInput;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

/**
 * @property string username
 * @property string email
 * @property string password
 * @property string password_confirmation
 * @property string captcha
 */
class CreateUserRequest extends FormRequest
{
    use SanitizesInput;

    protected $stopOnFirstFailure = true;

    protected string $route = 'auth.register';

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
            'username' => ['required', 'min:3', 'string', 'unique:users'],
            'email' => ['required', 'min:3', 'email', 'unique:users', new EmailTypo()],
            'password' => ['required', 'min:8', 'confirmed', 'regex:/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])(?=.{8,})/']
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
            'string' => __('common.required'),
            'email' => __('common.wrong_email'),
            'min' => __('common.minChr'),
            'confirmed' => __('common.passwords_not_equal'),
            'regex' => __('common.strong_password'),
            'username.unique' => __('register.used_username'),
            'email.unique' => __('register.used_email')
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
        $type = 'error';
        $key = $validator->errors()->keys();
        $key = $key[0];
        $value = $validator->errors()->first();

        $failedField = $validator->failed();
        if (isset($failedField['email']) && isset($failedField['email']['App\Rules\EmailTypo']) && $key === 'email') {
            $type = 'info';
        }

        if ($this->wantsJson()) {
            $response = response()->json(['type' => $type, 'field' => $key, 'message' => $value]);
        } else {
            $response = redirect()->route($this->route)->withErrors($validator);
        }

        throw new ValidationException($validator, $response);
    }
}
