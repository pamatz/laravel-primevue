<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        $user = $request->user();

        $navigation = collect(config('navigation'))
            ->map(function (array $section) use ($user): array {
                $items = collect($section['items'] ?? [])
                    ->filter(function (array $item) use ($user): bool {
                        $permission = $item['permission'] ?? null;

                        if ($permission === null) {
                            // Ítems sin permiso explícito solo se muestran a usuarios autenticados.
                            return $user !== null;
                        }

                        return $user?->hasPermission($permission) ?? false;
                    })
                    ->values()
                    ->all();

                return [
                    'label' => $section['label'],
                    'items' => $items,
                ];
            })
            ->filter(fn (array $section): bool => count($section['items']) > 0)
            ->values()
            ->all();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'false',
            'navigation' => $navigation,
        ];
    }
}
