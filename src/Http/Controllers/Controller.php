<?php

namespace Infinity\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Infinity\Events\FileUploadedEvent;
use Infinity\Events\ModelChangedEvent;
use Infinity\Exceptions\InfinityAuthorizationException;
use Infinity\Facades\Infinity;
use Infinity\FormFields\PasswordHandler;
use Infinity\FormFields\RelationshipHandler;
use Infinity\FormFields\TextHandler;
use Infinity\Http\Controllers\Traits\AlertsMessages;
use Infinity\Http\Controllers\Traits\ParsesRelationships;
use Infinity\Models\DataType;
use Infinity\Resources\Fields\FieldSet;
use Infinity\Resources\Resource;

abstract class Controller extends BaseController
{
    use AlertsMessages, DispatchesJobs, ValidatesRequests, AuthorizesRequests, ParsesRelationships;

    protected ?string $gate = null;
    protected ?string $backRoute = null;
    protected ?string $backRouteDisplay = null;

    /**
     * Get the slug from the request or named route.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    public function getSlug(Request $request): string
    {
        return $request->slug ?? explode('.', $request->route()->getName())[1];
    }

    /**
     * Create the appropriate model instance and save to the database.
     *
     * @param \Illuminate\Http\Request            $request
     * @param \Infinity\Resources\Fields\FieldSet $fields
     * @param \Infinity\Resources\Resource        $resource
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function insertUpdateData(Request $request, FieldSet $fields, Resource $resource): Model
    {
        $attributes = $this->removeForeignRelationships(Arr::only($request->all(),
            Arr::except($fields->boundColumns()->toArray(), $fields->model()->getFillable())
        ), $fields);

        $modelAttributes = array_merge($attributes, $this->handleUploadedFiles($request));

        if(count($modelAttributes) <= 0) {
            throw new \Exception(sprintf("No attributes when limiting attributes to fillable fields on [%s] and bound columns on [%s] - are you sure they've been made fillable?", $fields->model()::class, $resource::class));
        }

        DB::beginTransaction();

        $model = $fields->model()->getKey()
            ? $fields->model()->fill($modelAttributes)
            : $fields->model()->newInstance($modelAttributes);

        if($model->save()) {
            $fields->setModel($model);
        }

        $this->updateForeignRelationships($request->all(), $fields->getForeignRelationships(), $fields->model());

        DB::commit();

        ModelChangedEvent::dispatch($fields->model());

        return $model;
    }

    /**
     * Handle any uploaded files.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function handleUploadedFiles(Request $request): array
    {
        if(empty($request->allFiles())) {
            return [];
        }

        $files = [];

        foreach($request->allFiles() as $field => $uploadedFile) {
            /** @var \Illuminate\Http\UploadedFile $uploadedFile */
            $filename = sprintf("%s.%s", md5(microtime(true)), $uploadedFile->getClientOriginalExtension());
            $file = $uploadedFile->move(uploads_path(), $filename);

            FileUploadedEvent::dispatch($file);

            $files[$field] = uploads_url($file->getFilename());
        }

        return $files;
    }

    /**
     * Get the authorisation gate.
     *
     * @param string $gate
     *
     * @return void
     */
    protected function setGate(string $gate)
    {
        $this->gate = $gate;
    }

    /**
     * Get the authorisation gate.
     *
     * @param string $default
     *
     * @return string
     */
    protected function getGate(string $default): string
    {
        return !empty($this->gate)
            ? $this->gate
            : $default;
    }

    /**
     * Check the gate with authorize() method with a default fallback.
     *
     * @param string $default
     * @param array  $arguments
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function checkGate(string $default, array $arguments = []): void
    {
        try {
            $this->authorize(
                $this->getGate($default),
                $arguments
            );
        } catch(AuthorizationException $authorizationException) {
            throw new InfinityAuthorizationException($authorizationException->getMessage(), $authorizationException->getCode(), $authorizationException);
        }
    }

    /**
     * Set the back route.
     *
     * @param string      $route
     * @param string|null $displayName
     *
     * @return void
     */
    protected function setBackRoute(string $route, ?string $displayName = null)
    {
        $this->backRoute = $route;
        $this->backRouteDisplay = $displayName;
    }

    /**
     * Get the back route.
     *
     * @param $default
     *
     * @return string
     */
    protected function getBackRoute($default): string
    {
        return !empty($this->backRoute)
            ? $this->backRoute
            : $default;
    }

    /**
     * Get the text that should be displayed for a "back" route.
     *
     * @param $default
     *
     * @return string
     */
    protected function getBackRouteDisplay($default): string
    {
        return !empty($this->backRouteDisplay)
            ? $this->backRouteDisplay
            : $default;
    }
}
