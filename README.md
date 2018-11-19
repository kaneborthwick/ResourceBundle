<h1>composer require towersystems/resource-bundle</h1>


Register a resource that can be used in your application. 

Resource Controller, Factory, Repository, and CRUD api will be generated for your resource.

e.g

<h2>Register a new resource</h2>
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

<h2>Create API Routes</h2>
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
