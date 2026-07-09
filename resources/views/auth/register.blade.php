@extends('layouts.app')

@section('title', 'Inscription')

@section('content')
<div class="mx-auto max-w-2xl bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
    <h1 class="text-2xl font-semibold mb-3">Créer votre compte SaaS-Share</h1>
    <p class="text-sm text-slate-600 mb-6">Choisissez entre créer une entreprise ou rejoindre une équipe existante.</p>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Nom</span>
                <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded border border-slate-300 bg-white p-2 shadow-sm transition hover:border-slate-400 hover:ring-1 hover:ring-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20" />
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Email</span>
                <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 block w-full rounded border border-slate-300 bg-white p-2 shadow-sm transition hover:border-slate-400 hover:ring-1 hover:ring-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20" />
            </label>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Mot de passe</span>
                <input type="password" name="password" required class="mt-1 block w-full rounded border border-slate-300 bg-white p-2 shadow-sm transition hover:border-slate-400 hover:ring-1 hover:ring-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20" />
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Confirmation</span>
                <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded border border-slate-300 bg-white p-2 shadow-sm transition hover:border-slate-400 hover:ring-1 hover:ring-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20" />
            </label>
        </div>

        <div class="space-y-2">
            <label class="flex items-center gap-3">
                <input type="radio" name="action" value="create" checked onchange="toggleForms()" class="text-slate-900" />
                <span class="font-medium">Créer une entreprise</span>
            </label>
            <label class="flex items-center gap-3">
                <input type="radio" name="action" value="join" onchange="toggleForms()" class="text-slate-900" />
                <span class="font-medium">Rejoindre une équipe existante</span>
            </label>
        </div>

        <div id="create-company" class="space-y-4">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Nom de l'entreprise</span>
                <input type="text" name="workspace_name" value="{{ old('workspace_name') }}" class="mt-1 block w-full rounded border border-slate-300 bg-white p-2 shadow-sm transition hover:border-slate-400 hover:ring-1 hover:ring-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20" />
            </label>
            <p class="text-sm text-slate-500">Un code unique sera généré pour inviter votre équipe (ex : ALPHA1).</p>
        </div>

        <div id="join-team" class="hidden space-y-4">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Code de l'équipe</span>
                <input type="text" name="workspace_code" value="{{ old('workspace_code') }}" class="mt-1 block w-full rounded border border-slate-300 bg-white p-2 shadow-sm transition hover:border-slate-400 hover:ring-1 hover:ring-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20" />
            </label>
            <p class="text-sm text-slate-500">Rejoignez un espace déjà existant avec le code d'invitation.</p>
        </div>

        <button type="submit" class="w-full rounded bg-slate-900 px-4 py-3 text-white text-sm font-medium hover:bg-slate-700">S'inscrire</button>
    </form>
</div>

<script>
    function toggleForms() {
        const action = document.querySelector('input[name="action"]:checked').value;
        document.getElementById('create-company').classList.toggle('hidden', action !== 'create');
        document.getElementById('join-team').classList.toggle('hidden', action !== 'join');
    }

    document.addEventListener('DOMContentLoaded', toggleForms);
</script>
@endsection
