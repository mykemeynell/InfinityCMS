<?php

namespace Infinity\Http\Controllers;

use Illuminate\View\View;
use Infinity\Facades\Infinity;

class InfinityDashboardController extends InfinityBaseController
{
    /**
     * Show the dashboard.
     *
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showDashboard(): View
    {
        $this->authorize('dashboard.browse');

        $resources = array_merge_recursive(
            Infinity::resources('core'),
            Infinity::resources()
        );

        $cards = collect();
        foreach($resources as $resource) {
            /** @var \Infinity\Resources\Resource $resource */
            foreach($resource->cards() as $cardClass) {
                /** @var \Infinity\Cards\Card $card */
                $card = app($cardClass, ['resource' => $resource]);

                /** @var \Infinity\Models\Users\User $user */
                $user = !auth()->check() ?: auth()->user();

                if(!in_array($user->group->name, $card::$groups)) {
                    continue;
                }

                $cards->add($card->render());
            }
        }

        return Infinity::view('dashboard.dashboard', compact('cards'));
    }
}
