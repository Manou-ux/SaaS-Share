<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'action' => ['required', 'in:create,join'],
            'workspace_name' => ['nullable', 'string', 'max:255'],
            'workspace_code' => ['nullable', 'string', 'max:10'],
        ]);

        if ($request->action === 'create') {
            $request->validate([
                'workspace_name' => ['required', 'string', 'max:255'],
            ]);

            $workspace = Workspace::create([
                'name' => $request->workspace_name,
                'code' => $this->makeWorkspaceCode($request->workspace_name),
                'plan' => 'free',
            ]);
        } else {
            $request->validate([
                'workspace_code' => ['required', 'string', 'max:10'],
            ]);

            $workspace = Workspace::where('code', strtoupper($request->workspace_code))->first();

            if (! $workspace) {
                return back()->withErrors(['workspace_code' => 'Ce code d’équipe est invalide.'])->withInput();
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'workspace_id' => $workspace->id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    protected function makeWorkspaceCode(string $name): string
    {
        $slug = preg_replace('/[^A-Z]/', '', strtoupper($name));
        $slug = str_pad(substr($slug, 0, 5), 5, 'X');
        $code = substr($slug, 0, 5) . rand(0, 9);

        while (Workspace::where('code', $code)->exists()) {
            $code = substr($slug, 0, 5) . rand(0, 9);
        }

        return $code;
    }
}
