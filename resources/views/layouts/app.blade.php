<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>{{ config('app.name', 'SaaS-Share') }} - @yield('title')</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900">
        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
            <header class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
                <div>
                    <a href="{{ route('dashboard') }}" class="text-xl font-semibold text-slate-900">Workspace Hub</a>
<p class="text-xs text-slate-500 mb-1">
    Plateforme SaaS-Share: multi-tenant, partage de fichiers et chat en temps réel.
</p>
                    @auth
                        <p class="text-sm text-slate-600">Espace : {{ auth()->user()->workspace->name }} · Code : {{ auth()->user()->workspace->code }} · Plan : {{ ucfirst(auth()->user()->workspace->plan) }}</p>
                    @endauth
                </div>
                <nav class="flex flex-wrap gap-3 items-center text-sm">
                    @auth
                        <button type="button" id="settingsButton" class="group inline-flex items-center justify-center h-11 w-11 rounded-lg border border-slate-300 bg-white text-slate-900 hover:bg-slate-900 shadow-sm transition-colors" aria-haspopup="dialog" aria-controls="settingsDrawer" aria-expanded="false" title="Paramètres">
                            <span class="sr-only">Ouvrir les paramètres</span>
                            <img
                                src="{{ asset('images/settings-gear.png') }}"
                                alt="Paramètres"
                                class="h-7 w-7 select-none group-hover:invert transition-[filter]"
                                draggable="false"
                            />
                        </button>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100">Déconnexion</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 border border-slate-300 rounded text-slate-700 hover:bg-slate-100">Connexion</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-slate-900 text-white rounded">Inscription</a>
                    @endauth
                </nav>
            </header>

            @if (session('success'))
                <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded border border-rose-200 bg-rose-50 p-4 text-sm text-rose-900">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

        @auth
        <div id="settingsOverlay" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[1px] opacity-0 pointer-events-none transition-opacity duration-200" aria-hidden="true"></div>
        <aside id="settingsDrawer" class="fixed top-0 right-0 h-full w-[340px] max-w-[90vw] bg-white shadow-2xl border-l border-slate-200 translate-x-full transition-transform duration-300 ease-out" role="dialog" aria-modal="true" aria-label="Paramètres" aria-hidden="true">
            <div class="h-full flex flex-col">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                    <div class="font-semibold text-slate-900">Paramètres</div>
                    <button type="button" id="settingsClose" class="h-9 w-9 inline-flex items-center justify-center rounded border border-slate-300 text-slate-700 hover:bg-slate-100" aria-label="Fermer">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                    <details class="rounded border border-slate-200 bg-white px-4 py-3">
                        <summary class="cursor-pointer select-none font-medium text-slate-900">Paramètres du compte</summary>
                        <div class="mt-3 space-y-4">
                            <form method="POST" action="{{ route('profile.name') }}" class="space-y-2">
                                @csrf
                                <label class="block">
                                    <span class="text-xs font-medium text-slate-600">Nom (pseudo)</span>
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ auth()->user()->name }}"
                                        class="mt-1 block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm transition hover:border-slate-400 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20"
                                        required
                                        minlength="2"
                                        maxlength="50"
                                    />
                                </label>
                                <button type="submit" class="w-full rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                                    Enregistrer le pseudo
                                </button>
                            </form>

                            <div class="rounded border border-slate-200 bg-slate-50 p-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Plan</div>
                                        <div class="mt-1 text-sm font-semibold text-slate-900">
                                            {{ auth()->user()->workspace->plan === 'free' ? 'Free' : 'Premium' }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if (auth()->user()->workspace->plan === 'free')
                                            <form method="POST" action="{{ route('workspace.upgrade') }}">
                                                @csrf
                                                <button type="submit" class="rounded bg-gradient-to-r from-amber-500 to-yellow-400 px-3 py-2 text-xs font-semibold text-amber-950 shadow-sm hover:from-amber-400 hover:to-yellow-300 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                                                    Passer Premium
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('workspace.downgrade') }}">
                                                @csrf
                                                <button type="submit" class="rounded bg-gradient-to-r from-emerald-500 to-emerald-400 px-3 py-2 text-xs font-semibold text-emerald-950 shadow-sm hover:from-emerald-400 hover:to-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2">
                                                    Passer Free
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2 text-xs text-slate-500">
                                    Identique au bouton du dashboard (simulation).
                                </div>
                            </div>

                            <div class="rounded border border-slate-200 bg-white p-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Espace</div>
                                        <div class="mt-1 text-sm font-semibold text-slate-900">{{ auth()->user()->workspace->name }}</div>
                                        <div class="text-xs text-slate-500">Option démo fictive (ne change rien)</div>
                                    </div>
                                    <span class="text-xs rounded-full bg-slate-100 text-slate-600 px-2 py-1">Démo</span>
                                </div>
                                <label class="mt-3 block">
                                    <span class="text-xs font-medium text-slate-600">Changer d’espace (fictif)</span>
                                    <select class="mt-1 block w-full rounded border border-slate-300 bg-white px-3 py-2 text-sm" disabled>
                                        <option selected>{{ auth()->user()->workspace->name }} (actuel)</option>
                                        <option>Beta (démo)</option>
                                        <option>Gamma (démo)</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                    </details>

                    <details class="rounded border border-slate-200 bg-white px-4 py-3">
                        <summary class="cursor-pointer select-none font-medium text-slate-900">Mode sombre</summary>
                        <div class="mt-3 space-y-3">
                            <div class="flex items-center justify-between gap-3 rounded border border-slate-200 bg-slate-50 p-3">
                                <div>
                                    <div class="text-sm font-medium text-slate-900">Activer le mode sombre</div>
                                    <div class="text-xs text-slate-500">Démo uniquement (n’applique pas encore le thème)</div>
                                </div>
                                <label class="inline-flex items-center cursor-pointer select-none">
                                    <input id="darkModeToggle" type="checkbox" class="sr-only" />
                                    <span class="relative inline-flex h-6 w-11 items-center rounded-full bg-slate-300 transition-colors" data-switch-track>
                                        <span class="inline-block h-5 w-5 translate-x-1 rounded-full bg-white transition-transform shadow" data-switch-knob></span>
                                    </span>
                                </label>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-600">
                                <span>État</span>
                                <span id="darkModeState" class="rounded-full bg-slate-100 px-2 py-1 text-slate-700">Désactivé</span>
                            </div>
                        </div>
                    </details>

                    <details class="rounded border border-slate-200 bg-white px-4 py-3">
                        <summary class="cursor-pointer select-none font-medium text-slate-900">Aide</summary>
                        <div class="mt-3 text-sm text-slate-700 space-y-3">
                            <div class="font-semibold text-slate-900">SCÉNARIO DE DÉMO AIDE</div>
                            <div class="text-slate-600">Cette app permet de dérouler facilement ce scénario :</div>
                            <ol class="list-decimal list-inside space-y-2">
                                <li><span class="font-medium">Étape 1</span> : Inscription de Jean qui crée l'espace "<span class="font-medium">Alpha</span>" (génère le code <span class="font-medium">ALPHA1</span>) et uploade un fichier de <span class="font-medium">100 Ko</span>.</li>
                                <li><span class="font-medium">Étape 2</span> : Inscription de Lili qui rejoint l'espace "<span class="font-medium">Alpha</span>" avec le code <span class="font-medium">ALPHA1</span>. Elle voit le fichier de Jean, le télécharge, et lui écrit un message dans le chat. Jean voit le message.</li>
                                <li><span class="font-medium">Étape 3</span> : Inscription de Sophie qui crée l'espace "<span class="font-medium">Beta</span>". Son tableau de bord (fichiers et chat) est totalement vide et isolé de l'espace Alpha.</li>
                                <li><span class="font-medium">Étape 4</span> : Atteinte de la limite de <span class="font-medium">3 fichiers</span> dans l'espace Alpha, affichage du blocage, et simulation du passage au plan <span class="font-medium">Premium</span> pour débloquer l'upload illimité.</li>
                            </ol>
                        </div>
                    </details>
                </div>

                <div class="px-5 py-4 border-t border-slate-200 text-xs text-slate-500">
                    Astuce : appuie sur <span class="font-medium">Échap</span> pour fermer.
                </div>
            </div>
        </aside>

        <script>
            (function () {
                const btn = document.getElementById('settingsButton');
                const overlay = document.getElementById('settingsOverlay');
                const drawer = document.getElementById('settingsDrawer');
                const closeBtn = document.getElementById('settingsClose');

                if (!btn || !overlay || !drawer || !closeBtn) return;

                let lastActive = null;

                const openDrawer = () => {
                    lastActive = document.activeElement;
                    btn.setAttribute('aria-expanded', 'true');
                    overlay.classList.remove('opacity-0', 'pointer-events-none');
                    overlay.classList.add('opacity-100');
                    drawer.classList.remove('translate-x-full');
                    drawer.setAttribute('aria-hidden', 'false');
                    overlay.setAttribute('aria-hidden', 'false');
                    closeBtn.focus();
                };

                const closeDrawer = () => {
                    btn.setAttribute('aria-expanded', 'false');
                    overlay.classList.add('opacity-0', 'pointer-events-none');
                    overlay.classList.remove('opacity-100');
                    drawer.classList.add('translate-x-full');
                    drawer.setAttribute('aria-hidden', 'true');
                    overlay.setAttribute('aria-hidden', 'true');
                    if (lastActive && typeof lastActive.focus === 'function') lastActive.focus();
                };

                btn.addEventListener('click', openDrawer);
                closeBtn.addEventListener('click', closeDrawer);
                overlay.addEventListener('click', closeDrawer);
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') closeDrawer();
                });

                const toggle = document.getElementById('darkModeToggle');
                const state = document.getElementById('darkModeState');
                const track = drawer.querySelector('[data-switch-track]');
                const knob = drawer.querySelector('[data-switch-knob]');

                const applyDemoDarkState = (enabled) => {
                    if (!toggle || !state || !track || !knob) return;
                    toggle.checked = Boolean(enabled);
                    state.textContent = enabled ? 'Activé (démo)' : 'Désactivé';
                    track.classList.toggle('bg-slate-900', enabled);
                    track.classList.toggle('bg-slate-300', !enabled);
                    knob.classList.toggle('translate-x-6', enabled);
                    knob.classList.toggle('translate-x-1', !enabled);
                };

                if (toggle) {
                    const saved = localStorage.getItem('demo_dark_mode');
                    applyDemoDarkState(saved === '1');
                    toggle.addEventListener('change', () => {
                        const enabled = toggle.checked;
                        localStorage.setItem('demo_dark_mode', enabled ? '1' : '0');
                        applyDemoDarkState(enabled);
                    });
                }
            })();
        </script>
        @endauth
    </body>
</html>
