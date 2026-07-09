@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="mx-auto max-w-md bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
    <h1 class="text-2xl font-semibold mb-3">Connexion</h1>
    <p class="text-sm text-slate-600 mb-6">Accédez à votre espace de partage d'équipe.</p>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <label class="block">
            <span class="text-sm font-medium text-slate-700">Email</span>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded border-slate-300 shadow-sm" />
        </label>

        <label class="block">
            <span class="text-sm font-medium text-slate-700">Mot de passe</span>
            <input type="password" name="password" required class="mt-1 block w-full rounded border-slate-300 shadow-sm" />
        </label>

        <div class="flex items-center justify-between text-sm text-slate-600">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-900" />
                Se souvenir de moi
            </label>
        </div>

        <button type="submit" class="w-full rounded bg-slate-900 px-4 py-3 text-white text-sm font-medium hover:bg-slate-700">Se connecter</button>
    </form>
</div>
@endsection
