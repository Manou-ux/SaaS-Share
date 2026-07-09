<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $workspace = $user->workspace;

        $sort = (string) $request->query('sort', 'recent');

        $filesQuery = $workspace->files()->with('user');

        switch ($sort) {
            case 'oldest':
                $filesQuery->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $filesQuery->orderBy('original_name', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'name_desc':
                $filesQuery->orderBy('original_name', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'size_asc':
                $filesQuery->orderBy('file_size', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'size_desc':
                $filesQuery->orderBy('file_size', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $sort = 'recent';
                $filesQuery->orderBy('created_at', 'desc');
                break;
        }

        $files = $filesQuery->get();
        $myFilesCount = $workspace->files()->where('user_id', $user->id)->count();
        $remainingUploads = max(0, 3 - $myFilesCount);
        $messages = $workspace->messages()->with('user')->latest()->take(20)->get()->sortBy('created_at');

        return view('dashboard', compact('workspace', 'files', 'messages', 'myFilesCount', 'remainingUploads', 'sort'));
    }

    public function uploadFile(Request $request)
    {
        $user = $request->user();
        $workspace = $user->workspace;

        if ($workspace->plan === 'free') {
            $myFilesCount = $workspace->files()->where('user_id', $user->id)->count();
            if ($myFilesCount >= 3) {
                return back()->withErrors(['file' => 'Limite atteinte : en plan Free vous êtes limité à 3 fichiers par compte. Passez Premium pour débloquer.']);
            }
        }

        $request->validate([
            'file' => ['required', 'file', 'max:300'],
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store("workspaces/{$workspace->id}");

        $workspace->files()->create([
            'user_id' => $user->id,
            'original_name' => $uploaded->getClientOriginalName(),
            'storage_path' => $path,
            'file_size' => (int) ceil($uploaded->getSize() / 1024),
            'mime_type' => $uploaded->getClientMimeType(),
        ]);

        return back()->with('success', 'Fichier téléchargé avec succès.');
    }

    public function downloadFile(Request $request, $fileId)
    {
        $workspace = $request->user()->workspace;
        $file = File::where('id', $fileId)
            ->where('workspace_id', $workspace->id)
            ->firstOrFail();

        return Storage::download($file->storage_path, $file->original_name);
    }

    public function postMessage(Request $request)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:500'],
        ]);

        $workspace = $request->user()->workspace;

        $workspace->messages()->create([
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        return back()->with('success', 'Message envoyé.');
    }

    public function refreshMessages(Request $request)
    {
        $workspace = $request->user()->workspace;
        $userId = (int) $request->user()->id;

        $messages = $workspace->messages()
            ->with('user')
            ->latest()
            ->take(20)
            ->get()
            ->sortBy('created_at')
            ->map(function ($message) use ($userId) {
            $messageUserId = (int) $message->user_id;
            return [
                'id' => $message->id,
                'user_id' => $messageUserId,
                'user' => $message->user->name,
                'content' => $message->content,
                'created_at' => $message->created_at->diffForHumans(),
                'is_mine' => $messageUserId === $userId,
            ];
        });

        return response()->json($messages->values());
    }

    public function upgradePlan(Request $request)
    {
        $workspace = $request->user()->workspace;
        $workspace->update(['plan' => 'premium']);

        return back()->with('success', 'Plan Premium activé : la limite de fichiers est débloquée.');
    }

    public function downgradePlan(Request $request)
    {
        $workspace = $request->user()->workspace;
        $workspace->update(['plan' => 'free']);

        return back()->with('success', 'Plan Free activé : la limite de 3 fichiers est rétablie.');
    }

    public function updateName(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:50'],
        ]);

        $request->user()->update([
            'name' => $validated['name'],
        ]);

        return back()->with('success', 'Pseudo mis à jour.');
    }
}
