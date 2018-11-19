# composer require towersystems/resource-bundle


Register a resource that can be used in your application. 

Resource Controller, Factory, Repository, and CRUD api will be generated for your resource.

e.g

## Register a new resource

```
[
    'towersystems_resource' => [
        "resources" => [
            'tower.category' => [
                'classes' => [
                    'model' => Category::class,
                    'interface' => CategoryInterface::class,
                ],
            ],
        ],
    ],
];
```

## Create API Routes

```
[
    'towersystems_resource' => [
        'routes' => [
            'category' => [
                'alias' => 'tower.category',
                'only' => ['show', 'index', 'create', 'update', 'delete'],
            ],
        ],
    ],
];
```
