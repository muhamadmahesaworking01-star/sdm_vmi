<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Catat aksi perubahan yang berhasil dari seluruh user yang login.
        // GET tidak dicatat agar halaman navigasi tidak memenuhi activity log.
        $routeName = $request->route()?->getName();
        $method = strtoupper($request->method());
        $skipRoutes = [
            'login.store',
            'logout',
            // Aksi ini sudah memiliki log khusus di controller.
            'admin.users.store',
            'admin.users.update',
            'admin.users.destroy',
            'employees.destroy',
            'admin.employees.destroy',
        ];

        if ($request->user()
            && in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            && ! in_array($routeName, $skipRoutes, true)
            && $response->getStatusCode() < 400) {
            [$action, $description] = $this->activityDetails($routeName, $method);

            ActivityLog::create([
                'user_id' => $request->user()->getKey(),
                'action' => $action,
                'route' => $routeName,
                'method' => $method,
                'ip_address' => $request->ip(),
                'description' => $description,
            ]);
        }

        return $response;
    }

    private function activityDetails(?string $routeName, string $method): array
    {
        $routeName = strtolower($routeName ?? '');

        if (str_contains($routeName, 'documents.store')) {
            return ['upload', 'User mengunggah dokumen'];
        }

        if (str_contains($routeName, 'portfolios.store')) {
            return ['upload', 'User mengunggah portofolio'];
        }

        if (str_contains($routeName, 'contracts.extend')) {
            return ['contract', 'User memperpanjang kontrak'];
        }

        if (str_contains($routeName, 'contracts.extension.update')) {
            return ['contract', 'User mengubah perpanjangan kontrak'];
        }

        if (str_contains($routeName, 'contracts.extension.cancel')) {
            return ['contract', 'User membatalkan perpanjangan kontrak'];
        }

        if (str_contains($routeName, 'profile') || str_contains($routeName, 'employees.update')) {
            return ['update', 'User memperbarui profil atau data diri'];
        }

        if (str_contains($routeName, 'competencies.store')) {
            return ['create', 'User menambahkan kompetensi'];
        }

        return match ($method) {
            'POST' => ['create', 'User menambahkan data'],
            'PUT', 'PATCH' => ['update', 'User memperbarui data'],
            'DELETE' => ['delete', 'User menghapus data'],
            default => ['activity', 'User melakukan aktivitas'],
        };
    }
}
