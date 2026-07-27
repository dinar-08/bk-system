<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * Akun dikunci (secara tampilan/pesan) setelah gagal login
     * berturut-turut sebanyak User::MAX_FAILED_LOGIN_ATTEMPTS kali.
     * Tidak ada jeda waktu — begitu username & password yang benar
     * dimasukkan, counter langsung direset dan login berhasil seperti biasa.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $user = User::where('username', $this->string('username'))->first();

        if (Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            // Login berhasil -> lepas status terkunci akun ini.
            $user?->resetFailedLoginAttempts();

            return;
        }

        // Login gagal. Kalau username-nya memang terdaftar, tambah counter
        // gagalnya. Kalau username tidak ditemukan, tidak ada apa pun yang
        // perlu dihitung (tidak ada akun yang bisa "dikunci").
        $user?->incrementFailedLoginAttempts();

        if ($user && $user->isLoginLocked()) {
            throw ValidationException::withMessages([
                'username' => 'Akun terkunci karena 3x salah memasukkan username/password. '
                    . 'Silakan masukkan username dan password yang benar untuk membuka kembali.',
            ]);
        }

        throw ValidationException::withMessages([
            'username' => 'Username atau password tidak sesuai.',
        ]);
    }
}
