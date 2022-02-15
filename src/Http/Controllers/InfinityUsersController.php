<?php

namespace Infinity\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Infinity\Actions\ViewAction;
use Infinity\Facades\Infinity;
use Infinity\Models\Users\User;

class InfinityUsersController extends InfinityBaseController
{
    /**
     * @inheritDoc
     */
    public function update(Request $request, $id): RedirectResponse
    {
        if(empty($request->get('password'))) {
            $request->request->remove('password');
        }

        if(!$request->has('is_suspended')) {
            $request->request->set('is_suspended', false);
        }

        if(auth()->user()->getKey() === $id) {
            $this->setGate('users.editOwnProfile');
        }

        return parent::update($request, $id);
    }

    /**
     * Show the user profile.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $username
     *
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showProfile(Request $request, ?string $username = null): View
    {
        $slug = $this->getSlug($request);
        $this->authorize("{$slug}.viewProfile");

        $user = empty($username)
            ? auth()->user()
            : User::query()->where('username', $username)->firstOrFail();

        return Infinity::view('users.profile', [
            'user' => $user,
            'resource' => Infinity::resource($slug)
        ]);
    }

    /**
     * Show the edit user page with the current users details.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showEditMe(Request $request): View
    {
        $id = auth()->user()->getKey();
        $this->setGate('users.editOwnProfile');

        $this->setBackRoute(route('infinity.users.showMyProfile'), __('infinity::navigation.back_to_my_profile'));

        return parent::edit($request, $id);
    }
}
