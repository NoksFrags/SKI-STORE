<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Validator;
use App\Core\View;
use App\Models\User;

final class AuthController
{
    private const MAX_ATTEMPTS = 6;

    private const ATTEMPT_WINDOW = 600;

    public function showLogin(): void
    {
        View::render('auth/login', ['title' => 'Sign in'], 'layouts/auth');
    }

    public function login(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'since' => time()];

        if (time() - (int) $attempts['since'] > self::ATTEMPT_WINDOW) {
            $attempts = ['count' => 0, 'since' => time()];
        }

        if ((int) $attempts['count'] >= self::MAX_ATTEMPTS) {
            remember_old_input($_POST, ['email' => 'Too many failed attempts. Please wait a few minutes and try again.']);
            flash('Too many failed sign-in attempts. Please wait a few minutes.', 'error');
            redirect('/login');
        }

        $validator = (new Validator($_POST))
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->required('password', 'Password');

        if ($validator->fails()) {
            remember_old_input($_POST, $validator->errors());
            redirect('/login');
        }

        $user = Auth::attempt($email, $password);

        if ($user === null) {
            $attempts['count'] = (int) $attempts['count'] + 1;
            $_SESSION['login_attempts'] = $attempts;

            remember_old_input($_POST, ['email' => 'These credentials do not match an active account.']);
            flash('Wrong email or password.', 'error');
            redirect('/login');
        }

        unset($_SESSION['login_attempts']);

        Auth::login($user);
        User::touchLogin((int) $user['id']);

        flash('Welcome back, ' . $user['full_name'] . '.');

        $intended = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);

        if (is_string($intended) && str_starts_with($intended, '/') && ! str_starts_with($intended, '//')) {
            redirect($intended);
        }

        redirect($user['role'] === 'admin' ? '/admin' : '/account');
    }

    public function showRegister(): void
    {
        View::render('auth/register', ['title' => 'Create an account'], 'layouts/auth');
    }

    public function register(): void
    {
        $validator = (new Validator($_POST))
            ->required('full_name', 'Full name')
            ->maxLength('full_name', 120, 'Full name')
            ->required('email', 'Email')
            ->email('email', 'Email')
            ->maxLength('email', 180, 'Email')
            ->required('password', 'Password')
            ->minLength('password', 8, 'Password')
            ->matches('password_confirmation', 'password', 'Password confirmation', 'the password');

        $email = trim((string) ($_POST['email'] ?? ''));

        if ($validator->passes() && User::emailExists($email)) {
            $validator->add('email', 'An account with this email already exists.');
        }

        if (empty($_POST['terms'])) {
            $validator->add('terms', 'Please accept the terms to create an account.');
        }

        if ($validator->fails()) {
            remember_old_input($_POST, $validator->errors());
            redirect('/register');
        }

        $userId = User::create([
            'full_name'     => (string) $_POST['full_name'],
            'email'         => $email,
            'password_hash' => password_hash((string) $_POST['password'], PASSWORD_DEFAULT),
            'phone'         => trim((string) ($_POST['phone'] ?? '')) ?: null,
            'city'          => trim((string) ($_POST['city'] ?? '')) ?: null,
        ]);

        $user = User::find($userId);

        if ($user !== null) {
            Auth::login($user);
            User::touchLogin($userId);
        }

        flash('Welcome to Ski Store. Your account is ready.');

        redirect('/account');
    }

    public function logout(): void
    {
        Auth::logout();

        flash('You have been signed out.');

        redirect('/');
    }
}
