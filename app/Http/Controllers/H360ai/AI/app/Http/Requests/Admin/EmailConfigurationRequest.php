<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\CheckValidEmail;

class EmailConfigurationRequest extends FormRequest
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
        // No validation rules for GET requests
        if ($this->isMethod('get')) {
            return [];
        }

        // Common SMTP rules
        $smtpRules = [
            'required_if:protocol,smtp',
            'email',
            new CheckValidEmail
        ];

        return [
            'protocol'       => 'required|in:smtp,sendmail',
            'encryption'     => 'required_if:protocol,smtp',
            'smtp_host'      => 'required_if:protocol,smtp',
            'smtp_port'      => 'required_if:protocol,smtp',
            'smtp_email'     => $this->isSmtp() ? $smtpRules : ['nullable'],
            'from_address'   => $this->isSmtp() ? $smtpRules : ['nullable'],
            'from_name'      => 'required_if:protocol,smtp',
            'smtp_username'  => 'required_if:protocol,smtp',
            'smtp_password'  => 'required_if:protocol,smtp'
        ];
    }

    /**
     * Check if the selected protocol is SMTP.
     */
    private function isSmtp(): bool
    {
        return $this->input('protocol') === 'smtp';
    }
}
