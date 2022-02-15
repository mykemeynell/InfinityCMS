<?php

namespace Infinity\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Infinity\Actions\CreateAction;
use Infinity\Facades\Infinity;
use Infinity\Resources\Fields\FieldSet;
use Infinity\Resources\Resource;

abstract class InfinityBaseController extends Controller
{
    protected array $additionalViewData = [];
    protected ?string $orderByColumn = null;
    protected string $orderByDirection = 'DESC';

    /**
     * Handle the "index" action for a data type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request): View|Factory|Application
    {
        $slug = $this->getSlug($request);

        $this->checkGate("{$slug}.browse");

        $resource = Infinity::resource($slug);

        $orderByColumn = $this->getOrderByColumn($resource, $request);
        $orderDirection = $this->getOrderByDirection($request);

        $objects = $resource->modelClass()->newQuery()
            ->orderBy($orderByColumn, $orderDirection)
            ->get()->map(function ($object) use ($resource) {
                return new FieldSet($resource, $object);
            });

        $actions = [];
        foreach (Infinity::actions() as $action) {
            /** @var \Infinity\Actions\ActionInterface $actionClass */
            $actionClass = new $action($resource,
                $resource->modelClass()->newInstance()->toArray());
            if (
                !auth()->user()->can(sprintf("%s.%s",
                    $resource->getIdentifier(), $actionClass->action())) ||
                !$resource->isActionPossible($actionClass)
            ) {
                continue;
            }

            $actions[] = $actionClass;
        }

        $createAction = !in_array(CreateAction::class,
            $resource->excludedActions())
            ? new CreateAction($resource)
            : null;

        $viewName = Infinity::viewExists("{$slug}.browse")
            ? "{$slug}.browse"
            : "resources.browse";

        return Infinity::view($viewName,
            compact('resource', 'objects', 'actions', 'createAction'));
    }

    /**
     * Get the order by column either from the request or from the resource.
     *
     * @param \Infinity\Resources\Resource $resource
     * @param \Illuminate\Http\Request     $request
     *
     * @return string
     */
    protected function getOrderByColumn(
        Resource $resource,
        Request $request
    ): string {
        if ($request->has('orderBy')) {
            return $request->get('orderBy');
        }

        if (!empty($this->orderByColumn)) {
            return $this->orderByColumn;
        }

        return $resource->modelClass()->timestamps
            ? 'updated_at'
            : $resource->modelClass()->getKeyName();
    }

    /**
     * Get the order by direction.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function getOrderByDirection(Request $request): string
    {
        if ($request->has('orderDirection')) {
            $orderDirection = Str::upper(trim($request->get('orderDirection',
                'DESC')));
            if (in_array($orderDirection, ['ASC', 'DESC'])) {
                return $orderDirection;
            }
        }

        return $this->orderByDirection;
    }

    /**
     * Handle the create action for a data type.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function create(Request $request): View|Factory|Application
    {
        $slug = $this->getSlug($request);
        $resource = Infinity::resource($slug);

        $this->checkGate("{$slug}.add");

        $query = $resource->modelClass()->newQuery();

        // Use withTrashed() if model uses SoftDeletes and if toggle is selected
        if (in_array(SoftDeletes::class,
            class_uses_recursive($resource->modelClass()))) {
            /** @var Model|\Illuminate\Database\Eloquent\SoftDeletes $query */
            $query = $query->withTrashed();
        }

        $modelObject = call_user_func([$query, 'getModel']);

        return $this->createEditView($resource, $modelObject, $slug);
    }

    /**
     * Generate the create/edit view.
     *
     * @throws \Exception
     */
    protected function createEditView(
        Resource $resource,
        Model $model,
        string $slug
    ): View {
        $object = new FieldSet($resource, $model, 'formFields');

        $viewName = Infinity::viewExists("{$slug}.create-edit")
            ? "{$slug}.create-edit"
            : "resources.create-edit";

        $backRoute = $this->getBackRoute(infinity_route($resource->getIdentifier() . '.index'));
        $backRouteDisplay = $this->getBackRouteDisplay(__("infinity::navigation.back_to",
            ["name" => $resource->getDisplayName()]));

        return Infinity::view($viewName,
            compact('resource', 'object', 'backRoute', 'backRouteDisplay'));
    }

    /**
     * Handle the edit action for a data type.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function edit(Request $request, $id): View|Factory|Application
    {
        $slug = $this->getSlug($request);
        $resource = Infinity::resource($slug);

        $this->checkGate("{$slug}.edit");

        $query = $resource->modelClass()->newQuery();

        // Use withTrashed() if model uses SoftDeletes and if toggle is selected
        if (in_array(SoftDeletes::class,
            class_uses_recursive($resource->modelClass()))) {
            /** @var Model|\Illuminate\Database\Eloquent\SoftDeletes $query */
            $query = $query->withTrashed();
        }

        $modelObject = call_user_func([$query, 'findOrFail'], $id);

        return $this->createEditView($resource, $modelObject, $slug);
    }

    /**
     * Handle the store action for an entity of a data type.
     *
     * @throws \Throwable
     */
    public function store(Request $request
    ): JsonResponse|Redirector|RedirectResponse|Application {
        $slug = $this->getSlug($request);
        $resource = Infinity::resource($slug);

        $this->checkGate("{$slug}.add");

        // Validate fields with ajax
        // TODO: Validate request data against expected fields and their types

        try {
            $data = $this->insertUpdateData(
                $request,
                new FieldSet($resource, $resource->modelClass(), 'formFields'),
                $resource
            );

            if (!$request->has('_tagging')) {
                return redirect()->route("infinity.{$slug}.edit",
                    ['id' => $data->getKey()])->with([
                    'message' => __('infinity::generic.successfully_added_new') . " {$resource->getDisplayName(true)}",
                    'alert-type' => 'success',
                ]);
            } else {
                return response()->json(['success' => true, 'data' => $data]);
            }
        } catch (\Exception $exception) {
            if (!$request->has('_tagging')) {
                return redirect()->back()->with([
                    'message' => __('infinity::generic.creation_failed') . ": {$exception->getMessage()}",
                    'alert-type' => 'error',
                ]);
            } else {
                return response()->json(['success' => false]);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function showDelete(Request $request, $id): View
    {
        $slug = $this->getSlug($request);

        $this->checkGate("{$slug}.showDelete");

        $resource = Infinity::resource($slug);

        $query = $resource->modelClass()->newQuery();

        $modelObject = call_user_func([$query, 'findOrFail'], $id);
        $object = new FieldSet($resource, $modelObject);

        return Infinity::view('resources.delete',
            compact('resource', 'object'));
    }

    /**
     * @throws \Exception|\Throwable
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $slug = $this->getSlug($request);

        $this->checkGate("{$slug}.delete");

        $resource = Infinity::resource($slug);

        $query = $resource->modelClass()->newQuery();

        $modelObject = call_user_func([$query, 'findOrFail'], $id);
        // TODO: Implement a way to gather ALL fields to ensure any specified relationships are retrieved.
        $object = new FieldSet($resource, $modelObject, 'formFields');

        try {
            // Start a transaction so if anything fails we don't break anything.
            DB::beginTransaction();

            $object->model()->deleteOrFail();
            $this->deleteForeignRelationships($object->getForeignRelationships(),
                $object->model());

            DB::commit();

            return redirect()->route("infinity.{$resource->getIdentifier()}.index")
                ->with([
                    'message' => __('infinity::generic.successfully_deleted') . " {$resource->getDisplayName(true)}",
                    'alert-type' => 'success',
                ]);
        } catch (\Exception $exception) {
            return redirect()->route("infinity.{$resource->getIdentifier()}.index")
                ->with([
                    'message' => __('infinity::generic.failed_to_delete') . " {$resource->getDisplayName(true)}: {$exception->getMessage()}",
                    'alert-type' => 'error',
                ]);
        }
    }

    /**
     * Handle the update request of an object.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(
        Request $request,
        $id
    ): JsonResponse|Redirector|RedirectResponse|Application {
        $slug = $this->getSlug($request);

        $this->checkGate("{$slug}.edit");

        $resource = Infinity::resource($slug);

        // Compatibility with Model binding.
        $id = $id instanceof Model ? $id->{$id->getKeyName()} : $id;

        $query = $resource->modelClass()->newQuery();

        // Use withTrashed() if model uses SoftDeletes and if toggle is selected
        if (in_array(SoftDeletes::class,
            class_uses_recursive($resource->modelClass()))) {
            /** @var Model|\Illuminate\Database\Eloquent\SoftDeletes $query */
            $query = $query->withTrashed();
        }

        try {
            $modelObject = call_user_func([$query, 'findOrFail'], $id);
            $object = new FieldSet($resource, $modelObject, 'formFields');

            // TODO: Validate request data against expected fields and their types

            $data = $this->insertUpdateData($request, $object, $resource);

            $redirect = redirect()->route("infinity.{$slug}.edit",
                ['id' => $data->getKey()]);

            return $redirect->with([
                'message' => __('infinity::generic.successfully_updated') . " {$resource->getDisplayName(true)}",
                'alert-type' => 'success',
            ]);
        } catch (\Exception $exception) {
            if (!$request->has('_tagging')) {
                return redirect()->back()->with([
                    'message' => __('infinity::generic.creation_failed') . ": {$exception->getMessage()}",
                    'alert-type' => 'error',
                ]);
            } else {
                return response()->json(['success' => false]);
            }
        }
    }

    /**
     * Add additional view data.
     *
     * @param string $key
     * @param        $data
     *
     * @return void
     */
    protected function addAdditionalViewData(string $key, $data): void
    {
        $this->additionalViewData[$key] = value($data);
    }

    /**
     * Set both the order column and direction.
     *
     * @param string $orderBy
     * @param string|null $direction
     *
     * @return void
     */
    protected function setOrder(string $orderBy, ?string $direction): void
    {
        $this->setOrderBy($orderBy);

        if ($direction) {
            $this->setOrderDirection($direction);
        }
    }

    /**
     * Set the order by property.
     *
     * @param string $orderBy
     *
     * @return void
     */
    protected function setOrderBy(string $orderBy): void
    {
        $this->orderByColumn = $orderBy;
    }

    /**
     * Set the order direction.
     *
     * @param string $direction
     *
     * @return void
     */
    protected function setOrderDirection(string $direction): void
    {
        $this->orderByDirection = $direction;
    }
}
