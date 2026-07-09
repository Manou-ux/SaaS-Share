@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
    <section class="space-y-6">

        <!-- Profile -->
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="grid h-11 w-11 place-items-center rounded-2xl bg-slate-900 text-sm font-semibold text-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-sm text-slate-600">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Workspace</p>
                    <p class="text-sm font-semibold">{{ $workspace->name }}</p>
                    <p class="text-xs text-slate-500">Code : {{ $workspace->code }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold">Espace de stockage</h2>
                    <p class="text-sm text-slate-600">Tous les fichiers partagés pour votre équipe.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    @if ($workspace->plan === 'free')
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900 ring-1 ring-emerald-200">Free</span>
                    @else
                        <span class="rounded-full bg-gradient-to-r from-amber-200 to-yellow-100 px-3 py-1 text-xs font-semibold text-amber-950 ring-1 ring-amber-300">Premium</span>
                    @endif
                    @if ($workspace->plan === 'free')
                        <form method="POST" action="{{ route('workspace.upgrade') }}">
                            @csrf
                            <button
                                type="submit"
                                class="rounded bg-gradient-to-r from-amber-500 to-yellow-400 px-3 py-1.5 text-xs font-semibold text-amber-950 shadow-sm hover:from-amber-400 hover:to-yellow-300 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2"
                            >
                                Passer Premium
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('workspace.downgrade') }}">
                            @csrf
                            <button
                                type="submit"
                                class="rounded bg-gradient-to-r from-emerald-500 to-emerald-400 px-3 py-1.5 text-xs font-semibold text-emerald-950 shadow-sm hover:from-emerald-400 hover:to-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2"
                            >
                                Passer Free
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Fichiers</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $files->count() }}</p>
                    <p class="mt-1 text-sm text-slate-600">Mes fichiers : <span class="font-semibold text-slate-900">{{ $myFilesCount }}</span></p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Code d'équipe</p>
                    <p class="mt-2 font-semibold">{{ $workspace->code }}</p>
                </div>
                <div class="rounded-2xl bg-slate-50 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500">Limite</p>
                    <p class="mt-2 font-semibold">{{ $workspace->plan === 'free' ? '3 fichiers max (par compte)' : 'Illimité' }}</p>
                    @if ($workspace->plan === 'free')
                        <p class="mt-1 text-sm text-slate-600">Uploads restants : <span class="font-semibold text-slate-900">{{ $remainingUploads }}</span></p>
                    @endif
                </div>
            </div>
        </div>



        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Télécharger un fichier</h3>
            <form method="POST" action="{{ route('files.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" name="file" class="block w-full rounded border border-slate-300 p-2" required />
                <div class="flex items-center justify-between gap-4">
                    <p class="text-sm text-slate-500">Toutes extensions acceptées jusqu'à 300 Ko.</p>
                    <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-700">Uploader</button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
                <h3 class="text-lg font-semibold">Fichiers partagés</h3>
                <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <label class="text-sm text-slate-600" for="sort">Trier</label>
                    <select id="sort" name="sort" class="rounded border border-slate-300 bg-white px-3 py-2 text-sm" onchange="this.form.submit()">
                        <option value="recent" @selected(($sort ?? 'recent') === 'recent')>Plus récents</option>
                        <option value="oldest" @selected(($sort ?? 'recent') === 'oldest')>Plus anciens</option>
                        <option value="name_asc" @selected(($sort ?? 'recent') === 'name_asc')>Nom A → Z</option>
                        <option value="name_desc" @selected(($sort ?? 'recent') === 'name_desc')>Nom Z → A</option>
                        <option value="size_desc" @selected(($sort ?? 'recent') === 'size_desc')>Taille (grand → petit)</option>
                        <option value="size_asc" @selected(($sort ?? 'recent') === 'size_asc')>Taille (petit → grand)</option>
                    </select>
                </form>
            </div>
            @if ($files->isEmpty())
                <p class="text-sm text-slate-600">Aucun fichier partagé dans cet espace pour le moment.</p>
            @else
                <div class="space-y-3 {{ $files->count() > 5 ? 'max-h-80 overflow-y-auto pr-2' : '' }}">
                    @foreach ($files as $file)
                        <div class="flex flex-col gap-2 rounded-2xl border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold">{{ $file->original_name }}</p>
                                <p class="text-sm text-slate-500">
                                    {{ $file->file_size }} Ko · {{ $file->mime_type }}
                                    <span class="text-slate-300">·</span>
                                    <span>Uploadé par <span class="font-medium text-slate-700">{{ $file->user->name }}</span></span>
                                    <span class="text-slate-300">·</span>
                                    <span class="text-slate-400">{{ $file->created_at->format('d/m/Y H:i') }}</span>
                                </p>
                            </div>
                            <a href="{{ route('files.download', ['fileId' => $file->id]) }}" class="rounded bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-700">Télécharger</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="space-y-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold">Chat d'équipe</h3>
                <div class="flex items-center gap-3">
                    <p id="chat-status" class="text-xs text-slate-500"></p>
                    <button
                        id="chat-refresh"
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-50"
                        title="Rafraîchir"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M15.312 11.424a6.5 6.5 0 10-1.16 2.356.75.75 0 111.23.86 8 8 0 11.65-6.138h.718a.75.75 0 010 1.5h-2.5a.75.75 0 01-.75-.75v-2.5a.75.75 0 011.5 0v.85a6.47 6.47 0 011.312 3.822z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
            <div id="chat-box" class="space-y-2 overflow-y-auto pr-2 pb-2 {{ $messages->count() > 5 ? 'max-h-[22rem]' : '' }}">
                @forelse ($messages as $message)
                    @php($isMine = $message->user_id === auth()->id())
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] rounded-2xl border px-3 py-2.5 {{ $isMine ? 'border-slate-200 bg-slate-900 text-white' : 'border-slate-200 bg-slate-50 text-slate-900' }}">
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="text-xs font-semibold {{ $isMine ? 'text-white' : '' }}">{{ $message->user->name }}</p>
                                <p class="text-xs {{ $isMine ? 'text-slate-300' : 'text-slate-500' }}">{{ $message->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="mt-1 text-sm leading-snug {{ $isMine ? 'text-slate-100' : 'text-slate-700' }}">{{ $message->content }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-600">Le chat de l'équipe est vide. Envoyez le premier message !</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('messages.store') }}" class="space-y-4">
                @csrf
                <label class="block">
                    <span class="text-sm font-medium text-slate-700">Votre message</span>
                    <textarea
                        name="content"
                        rows="2"
                        placeholder="Écrivez votre message…"
                        title="Écrivez un message pour votre équipe"
                        class="mt-1 block w-full rounded border border-slate-300 bg-white p-2 shadow-sm transition hover:border-slate-400 hover:ring-1 hover:ring-slate-200 focus:border-slate-900 focus:ring-2 focus:ring-slate-900/20"
                        required
                    ></textarea>
                </label>
                <button type="submit" class="w-full rounded bg-slate-900 px-4 py-3 text-white text-sm font-medium hover:bg-slate-700">Envoyer</button>
            </form>
        </div>
    </section>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatBox = document.getElementById('chat-box');
        const chatRefreshButton = document.getElementById('chat-refresh');
        const chatStatus = document.getElementById('chat-status');
        const currentUserId = @json(auth()->id());

        if (!chatBox) return;

        function setStatus(text) {
            if (!chatStatus) return;
            chatStatus.textContent = text;
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderMessage(message) {
            const isMine = message.is_mine === true || Number(message.user_id) === Number(currentUserId);
            const wrapperClass = isMine ? 'justify-end' : 'justify-start';
            const bubbleClass = isMine
                ? 'border-slate-200 bg-slate-900 text-white'
                : 'border-slate-200 bg-slate-50 text-slate-900';
            const metaClass = isMine ? 'text-slate-300' : 'text-slate-500';
            const contentClass = isMine ? 'text-slate-100' : 'text-slate-700';
            const nameClass = isMine ? 'text-white' : '';

            return `
                <div class="flex ${wrapperClass}">
                    <div class="max-w-[85%] rounded-2xl border px-3 py-2.5 ${bubbleClass}">
                        <div class="flex items-baseline justify-between gap-2">
                            <p class="text-xs font-semibold ${nameClass}">${escapeHtml(message.user)}</p>
                            <p class="text-xs ${metaClass}">${escapeHtml(message.created_at)}</p>
                        </div>
                        <p class="mt-1 text-sm leading-snug ${contentClass}">${escapeHtml(message.content)}</p>
                    </div>
                </div>
            `;
        }

        let inFlight = null;

        async function refreshMessages(reason = '') {
            if (inFlight) return inFlight;

            const url = new URL(@json(route('messages.refresh')));
            url.searchParams.set('t', String(Date.now()));

            inFlight = (async () => {
                try {
                    setStatus(reason ? `Maj... (${reason})` : 'Maj...');
                    const response = await fetch(url.toString(), {
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store',
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        setStatus('Erreur');
                        return;
                    }

                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                        setStatus('Non JSON');
                        return;
                    }

                    const messages = await response.json();
                    chatBox.innerHTML = messages.map(renderMessage).join('');
                    chatBox.scrollTop = chatBox.scrollHeight;
                    setStatus(`Maj: ${new Date().toLocaleTimeString()}`);
                } catch (e) {
                    setStatus('Erreur');
                } finally {
                    inFlight = null;
                }
            })();

            return inFlight;
        }

        chatRefreshButton?.addEventListener('click', () => refreshMessages('manuel'));

        refreshMessages('auto');
        setInterval(() => refreshMessages('auto'), 3000);
    });
</script>
@endsection
