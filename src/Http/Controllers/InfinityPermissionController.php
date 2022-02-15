<?php

namespace Infinity\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Infinity\Facades\Infinity;

class InfinityPermissionController extends InfinityBaseController
{
    /**
     * @inheritDoc
     */
    public function edit(Request $request, $id): View|Factory|Application
    {
//        $this->addAdditionalViewData('groups', Infinity::model('Group')->newQuery()->get());

        return parent::edit($request, $id);
    }
}
