<?php

namespace App\Http\Controllers\Admin;

use App\Application\Audit\AuditLogger;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateAdminUserRequest;
use App\Http\Requests\Admin\PromoteUserToAdminRequest;
use App\Http\Requests\Admin\UpdateAdminUserStatusRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(Request $request): View
    {
        $this->ensureSuperAdmin();

        $search = trim((string) $request->query('q', ''));
        $role = (string) $request->query('role', '');

        $users = User::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($role, ['admin', 'manager', 'customer'], true), fn ($query) => $query->where('role', $role))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'filters' => [
                'q' => $search,
                'role' => $role,
            ],
            'roleCounts' => [
                'admin' => User::query()->where('role', 'admin')->count(),
                'manager' => User::query()->where('role', 'manager')->count(),
                'customer' => User::query()->where('role', 'customer')->count(),
            ],
        ]);
    }

    public function promote(PromoteUserToAdminRequest $request): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $user = User::query()->where('email', $request->string('email'))->firstOrFail();
        $before = $user->toArray();

        if ($user->role === UserRole::ADMIN) {
            return back()->with('status', 'Esse usuario ja possui permissao de admin.');
        }

        $user->update([
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $this->auditLogger->log('admin.user_promoted', $user, $before, $user->fresh()?->toArray(), [
            'promoted_email' => $user->email,
        ]);

        return back()->with('status', 'Usuario promovido para admin com sucesso.');
    }

    public function store(CreateAdminUserRequest $request): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'role' => UserRole::ADMIN,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->auditLogger->log('admin.user_created', $user, null, $user->toArray(), [
            'created_email' => $user->email,
            'created_role' => $user->role?->value,
        ]);

        return back()->with('status', 'Novo admin criado com sucesso.');
    }

    public function updateStatus(UpdateAdminUserStatusRequest $request, User $user): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $newStatus = $request->boolean('is_active');

        if ($user->id === auth()->id() && ! $newStatus) {
            return back()->withErrors([
                'is_active' => 'Voce nao pode desativar a propria conta.',
            ]);
        }

        $before = $user->toArray();

        $user->update([
            'is_active' => $newStatus,
        ]);

        $this->auditLogger->log('admin.user_status_updated', $user, $before, $user->fresh()?->toArray(), [
            'target_email' => $user->email,
            'new_is_active' => $newStatus,
        ]);

        return back()->with('status', $newStatus ? 'Usuario ativado com sucesso.' : 'Usuario desativado com sucesso.');
    }

    private function ensureSuperAdmin(): void
    {
        abort_unless(auth()->user()?->role === UserRole::ADMIN, 403);
    }
}
