# Infinity CMS 

## Installation

### Prerequisites

1. Already have your database configured in your ```.env``` file.
2. You have already set your ```APP_KEY``` _(you will be asked if you would like to do this as part of the installation if you haven't already set one)_.

### Installation with Composer

1. ```composer require mykemeynell/infinity```

2. Include the Infinity service provider in your ```config/app.php``` config

```php
'providers' => [
    // ...
    
    \Infinity\InfinityServiceProvider::class,
    
    // ...    
]
```

3. In ```config/auth.php``` change the ```providers > user > model``` key to ```\Infinity\Models\Users\User::class```.

4. Run ```php artisan infinity:install``` to install Infinity.

## Configuration

### Load Infinity Routes Differently

Infinity's routes are loaded by default without further configuration as part of
the Infinity Service Provider. However, if you would like to change the point at
which the routes are loaded in, you can do so by adding 
```\Infinity\Facades\Infinity::routes();``` to your routes file. This will also
prevent the service provider from loading them automatically.

This is useful if, for example; you have a generic ```Route::get('{slug}', ...)``` 
route defined - and you need to load Infinity routes beforehand, so they're not 
captured by this 'catchall'

### Environment Variables

| Variable                  | Type          | Default     | Description                                                                     |
|---------------------------|---------------|-------------|---------------------------------------------------------------------------------|
| INFINITY_CONFIG_CACHE_TTL | ```integer``` | ```86400``` | The number of seconds that any cached database settings will be remembered for. |
| CACHE_INFINITY_SETTINGS   | ```bool```    | ```true```  | Whether settings values should be cached when read.                             |


You can read more about environment configuration options [here](#Environment).

## Creating Resources

```php artisan infinity:make:resource <name>```

A new resource class will be created under ```app/Infinity/<name>.php``` - here 
you can modify how the resource is displayed and how particular aspects are
handled within Infinity.

## Cards

Cards are small snippet views of data that are rendered on the dashboard. To 
create a new card run:

```php artisan infinity:make:card <name>```

You will need to tell the card which view to render by specifying the view name
in the ```view(): string``` method. This view will be rendered in the cards
content area, so there is no need to create the card title.

To change the title displayed in the card header area, specify the following 
property with a value.

```php
public static string $title = 'My Card';
```

If you only want your card to be shown to specific groups, you can use the 
```group``` property.

```php
public static array $groups = ['admin'];
```

### Resource Options

| Option         | Description                                          |
|----------------|------------------------------------------------------|
| --no-migration | Do not create a migration when creating the resource |
| --no-model     | Do not create a model when creating the resource     |

## Basic Resource Configuration

```php
public static string $model;
```

Set the model that relates to the resource using the fully qualified namespace.

```php
public static bool $displayInNavigation = true;
```

Whether the resource object should be displayed in the sidebar **(Default: ```true```)**.

```php
public static string $icon;
```

Configure the icon of the resource, this can be a single FontAwesome icon, or
a FREE/PRO alternative if a PRO licence is configured. **(Default: ```'fas fa-infinity / fad fa-infinity'```)**

```php
public static ?string $controller;
```

The fully qualified namespace of the resource's controller.

If this is set, then it will be returned without further tests.

Otherwise, a class with the name ```<Resource>Controller``` will be tested in 
the ```App\Http\Controllers``` namespace. If such a class exists, then that 
class will be used for this resource, otherwise a fallback of
```\Infinity\Http\Controllers\InfinityBaseController::class``` will be used.


## Fields

To set the fields that are used for a resource you can modify the 
```Resource::fields()``` method to return an array of the fields that you would 
like outputted. The fields are output in the order they are specified.

### Field Types

For available field methods, see [Field Methods](#field-methods).

| Field Type   |
|--------------|
| ID           |
| Text         |
| Boolean      |
| DateTime     |
| Relationship |

#### Field Methods

| Field Type                | Available Methods       | Arguments                            | Default Value | Description                                                                                                                                                                                                                                                                  |
|---------------------------|-------------------------|--------------------------------------|---------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| ```Field::class```        |                         |                                      |               |                                                                                                                                                                                                                                                                              |
|                           | ```static``` ```make``` | ```string $field```                  |               | Creates a new instance of the field.                                                                                                                                                                                                                                         |
|                           | ```empty```             | ```$value```                         | ```null```    | Sets the value that is displayed in the event that the callback to ```display()``` is empty.                                                                                                                                                                                 |
|                           | ```hidden```            |                                      |               | Sets a field to hidden - it will not be output to the browser, but is still bound to a resource.                                                                                                                                                                             |
|                           | ```setDisplayName```    | ```string $name```                   |               | Set the name that a field outputs to the browser. For example - when outputting input labels or table headers.                                                                                                                                                               |
|                           | ```view```              |                                      |               | Set a view that is used to output a field.                                                                                                                                                                                                                                   |
|                           |                         | ```string $name```                   |               | The fully qualified view name.                                                                                                                                                                                                                                               |
|                           |                         | ```array $viewData```                | ```[]```      | Data that is passed to the view. If you specify your own views then you can specify additional data passed here, see [View Data](#view-data) for more information. <br/><br/>See [Conditional View Data](#conditional-view-data) for more information on conditional values. |
|                           | ```readOnly```          |                                      |               | Set the field to readOnly.                                                                                                                                                                                                                                                   | 
|                           | ```disabled```          |                                      |               | Set the field to disabled.                                                                                                                                                                                                                                                   | 
| ```ID::class```           |                         |                                      |               |                                                                                                                                                                                                                                                                              |
| ```Text::class```         |                         |                                      |               |                                                                                                                                                                                                                                                                              |
| ```Boolean::class```      |                         |                                      |               |                                                                                                                                                                                                                                                                              |
|                           | ```setValueIfFalse```   | ```string $value```                  |               | Set the value that is output when the boolean value is ```false```.                                                                                                                                                                                                          |
|                           | ```setValueIfTrue```    | ```string $value```                  |               | Set the value that is output when the boolean value is ```true```.                                                                                                                                                                                                           |
| ```DateTime::class```     |                         |                                      |               |                                                                                                                                                                                                                                                                              |
| ```Relationship::class``` |                         |                                      |               |                                                                                                                                                                                                                                                                              |
|                           | ```static``` ```make``` | ```string $fieldOrUsing```           |               | Can be used as an alternative to ```using(string $relationship)``` if relationship is a ```Many``` type where the key doesn't exist on the model table and is part of a pivot.                                                                                               |
|                           | ```by```                | ```string $method```                 |               | The method that is used on the related model when outputting the data to the browser.                                                                                                                                                                                        |
|                           | ```using```             | ```string $relationship```           |               | The relationship name on the resource that should be used to get the related model.                                                                                                                                                                                          |
| ```Link::class```         |                         |                                      |               |                                                                                                                                                                                                                                                                              |
|                           | ```to```                | ```string $routeName```              |               | Sets the route that the link should reference.                                                                                                                                                                                                                               |
|                           |                         | ```array $routeParamFieldBindings``` | ```[]```      | Bind a route parameter to a model column.                                                                                                                                                                                                                                    |
|                           | ```target```            | ```string $target```                 |               | Sets the value of the ```target``` attribute on the rendered link.                                                                                                                                                                                                           | 

#### View Data

##### Attributes

If you wish to pass custom attributes through to a rendered view - you should
do so using the ```attributes``` key.

```php
[
    'attributes' => [
        'class' => 'text-blue-300 text-xs'
    ]   
]
```

#### Conditional View Data

Specify conditional view attributes using the ```conditional``` array key. Any 
conditions that are evaluated to ```true``` will be merged with the attribute
with the same key from ```attributes``` in [Attributes](#attributes).

```php
[
    'conditional' => [
        'class' => [
            'column_name:column_value' => 'applied_classes',    
            'column_name:another_value' => 'alternative_classes',
        ]    
    ]
]
```

## Resource Configuration Methods

### ```fields(): array```

Fields that are shown on the ```resource.index``` route - these are used as 
table columns.

For information on possible field types, see [Field Types](#field-types).

```php
public function fields(): array
{
    return [
        ID::make()->hidden(),
        Text::make('name'),
        Text::make('email'),
    ];
}
```

### ```formFields(): array```

The configuration of this method is the same as ```fields(): array```, but these
fields are displayed when viewing resource routes that output forms.

If nothing is specified here, or the method is omitted then ```fields()``` is 
used instead.

```php
public function formFields(): array
{
    return array_merge_recursive($this->fields(), [
        Password::make('password'),
    ]);
}
```

### ```excludedActions(): array```

Methods that should be excluded from a resource - for example if you want to 
remove the ability to view a single instance of a resource, you can specify the
```ViewAction::class``` action. This will also prevent the ```resource.show```
resource route from being registered too.


```php
public function excludedActions(): array
{
    return [\Infinity\Actions\ViewAction::class];
}
```

### ```additionalRoutes(): array```

If you wish to add additional routes to a resource, then you can do so using the
```additionalRoutes``` method. This should return an array with values of
```\Infinity\Resources\Routes\AdditionalRoute::class```.

```php
public function additionalRoutes(): array
{
    return [
        AdditionalRoute::make('me')->setAction('showProfile')
    ];
}
```

#### Additional Route Methods


| Method                  | Arguments            | Description                                                                                              |
|-------------------------|----------------------|----------------------------------------------------------------------------------------------------------|
| ```static``` ```make``` | ```string $uri```    | Creates a new instance of the ```AdditionalRoute``` class, and sets the URI.                             |
| ```setMethod```         | ```string $method``` | The method that the route can be accessed over. GET, POST, PUT, PATCH, DELETE and OPTIONS are supported. |
| ```setName```           | ```string $name```   | Sets the name that the route will be assigned. It will be prefixed with "infinity.```resourceName```"    |
| ```setAction```         | ```string $action``` | Sets the action that the route will perform, this method should exist on the appropriate controller.     |


### ```additionalGates(): array```

Gates that can be added at a resource level. These can then be registered when
Infinity is in development mode by using the 
```infinity:debug:flush-permissions``` artisan command.

```php
public function additionalGates(): array
{
    return ['viewProfile'];
}
```

## Artisan Commands

| Command                                | Description                                                                                                                                                                                                                          |
|----------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| ```infinity:install```                 | Install Infinity.                                                                                                                                                                                                                    |
| ```infinity:admin```                   | Modify or create (with ```--create```) an admin user.                                                                                                                                                                                |
| ```infinity:make:resource```           | Create a new Infinity resource.                                                                                                                                                                                                      |
| ```infinity:debug:make-permissions```  | Creates the default permissions for a resource. Confirmation can be bypassed with ```--no-confirm``` option.                                                                                                                         |
| ```infinity:debug:flush-permissions``` | **Scans all resources**, and takes into account excluded and additional gates and actions to create or remove any permissions that are required by any resource. **Any actions are displayed and confirmed before action is taken.** |

## Using FontAwesome PRO

If you'd like to use FontAwesome PRO, you can use the following keys in your 
```.env``` file - by default FontAwesome FREE is used.

If no Integrity is set then an exception is thrown.

```shell
FONTAWESOME_LICENCE=
FONTAWESOME_SRC=
FONTAWESOME_INTEGRITY=
```

You can read more about environment configuration options [here](#Environment).

## Environment

### Infinity Debug/Development

To enable debug/development artisan commands in Infinity, add the following to 
your ```.env``` file.

```shell
INFINITY_DEV=true
```

## Events

| Event                  | Fired On                        |
|------------------------|---------------------------------|
| FileUploadedEvent      | File successfully uploaded      |
| ModelChangedEvent      | Model updated or created        |
| UserAuthenticatedEvent | User successfully authenticated |
