<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FullResource extends Command
{
    protected $signature   = 'make:full-resource {name} {--backend} {--frontend}';
    protected $description = 'Generate a module with Model, Migration, Controller, Request, Resource Collection, and Repository';

    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $type = $this->option('backend') ? 'Backend' : 'Frontend';

        $directory = app_path("Http/Controllers/$type");

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        // Model Create from Template
        $modelPath = app_path("Models/{$name}.php");

        if (!File::exists($modelPath)) {
            File::put($modelPath, $this->getModelTemplate($name));
            $this->info("Model $name created successfully using custom template!");
        }

        // Controller Create from Custom Template
        $controllerPath = app_path("Http/Controllers/$type/{$name}Controller.php");

        if (!File::exists($controllerPath)) {
            if (!File::exists(app_path('Console/Stubs/controller.stub'))) {
                throw new \Exception("Controller stub file not found!");
            }

            $controllerTemplate = file_get_contents(app_path('Console/Stubs/controller.stub'));

            // Replace placeholders like {{model}} with actual model name
            $controllerTemplate = str_replace('{{model}}', $name, $controllerTemplate);
            $controllerTemplate = str_replace('{{type}}', $type, $controllerTemplate);
            $controllerTemplate = str_replace('{{modelSnake}}', Str::snake($name), $controllerTemplate);
            $controllerTemplate = str_replace('{{modelCamel}}', Str::camel($name), $controllerTemplate);

            File::put($controllerPath, $controllerTemplate);
            $this->info("Controller $name created successfully!");
        }


        // Request Create from Custom Template
        $requestPath = app_path("Http/Requests/$type/{$name}Request.php");

        if (!File::exists(app_path('Console/Stubs/request.stub'))) {
            throw new \Exception("Request stub file not found!");
        }

        $requestTemplate = file_get_contents(app_path('Console/Stubs/request.stub'));
        $requestTemplate = str_replace('{{requestName}}', $name, $requestTemplate);

        File::put($requestPath, $requestTemplate);
        $this->info("Request file created successfully!");


        // Resource Create from Custom Template
        $resourcePath = app_path("Http/Resources/$type/{$name}Resource.php");

        if (!File::exists(app_path('Console/Stubs/resource.stub'))) {
            throw new \Exception("Resource stub file not found!");
        }

        $resourceTemplate = file_get_contents(app_path('Console/Stubs/resource.stub'));
        $resourceTemplate = str_replace('{{resourceName}}', $name, $resourceTemplate);

        File::put($resourcePath, $resourceTemplate);
        $this->info("Resource file created successfully!");


        // Collection Create from Custom Template
        $collectionPath = app_path("Http/Resources/$type/{$name}Collection.php");

        if (!File::exists(app_path('Console/Stubs/collection.stub'))) {
            throw new \Exception("Collection stub file not found!");
        }

        $collectionTemplate = file_get_contents(app_path('Console/Stubs/collection.stub'));
        $collectionTemplate = str_replace('{{collectionName}}', $name, $collectionTemplate);

        File::put($collectionPath, $collectionTemplate);
        $this->info("Collection file created successfully!");


        // Migration Create from Custom Template
        $migrationPath = database_path('migrations/' . date('Y_m_d_His') . '_create_' . Str::snake(Str::plural($name)) . '_table.php');

        if (!File::exists(app_path('Console/Stubs/migration.stub'))) {
            throw new \Exception("Migration stub file not found!");
        }

        $migrationTemplate = file_get_contents(app_path('Console/Stubs/migration.stub'));
        $migrationTemplate = str_replace('{{tableName}}', Str::snake(Str::plural($name)), $migrationTemplate);

        File::put($migrationPath, $migrationTemplate);
        $this->info("Migration file created successfully!");

        // Repository Create from Custom Template
        $repoPath = app_path("Repositories/{$name}Repository.php");

        // Check if the Repositories directory exists, if not, create it
        if (!File::exists(app_path('Repositories'))) {
            File::makeDirectory(app_path('Repositories'), 0755, true, true);
        }

        // Check if the repository stub file exists
        if (!File::exists(app_path('Console/Stubs/repository.stub'))) {
            throw new \Exception("Repository stub file not found!");
        }

        // Get the contents of the repository template stub
        $repoTemplate = file_get_contents(app_path('Console/Stubs/repository.stub'));

        // Replace placeholders in the template with dynamic data
        $repoTemplate = str_replace('{{repositoryName}}', $name, $repoTemplate);
        $repoTemplate = str_replace('{{repositoryCamel}}', Str::camel($name), $repoTemplate);

        // Save the generated repository file to the specified path
        File::put($repoPath, $repoTemplate);

        $this->info("Repository $name created successfully in the Repositories folder!");


        // Now, generate the routes in `routes/api.php`
        $routeContent = $this->generateRoutes($name, $type);
        $this->addRoutesToApiFile($routeContent);

        $this->info("Routes for $name added to routes/api.php!");

    }

    private function generateRoutes($name, $type)
    {
        $kebabName = Str::kebab($name); // Convert to kebab-case
        $controllerName = "{$name}Controller"; // Dynamic controller name

        // Construct the route content for categories
        return "
        Route::prefix('{$kebabName}s')->group(function () {
            Route::controller(App\\Http\\Controllers\\{$type}\\{$controllerName}::class)->group(function () {
                Route::get('/',                         'index');
                Route::get('/list',                     'list');
                Route::post('/',                        'store');
                Route::get('/trash',                    'trashList');
                Route::get('/{id}',                     'show');
                Route::put('/{id}',                     'update');
                Route::delete('/{id}',                  'destroy');
                Route::put('/{id}/restore',             'restore');
                Route::delete('/{id}/permanent-delete', 'permanentDelete');
            });
        });";
    }


    private function getModelTemplate($name)
    {
        $stubPath = app_path('Console/Stubs/model.stub');

        if (!File::exists($stubPath)) {
            throw new \Exception("Model stub file not found!");
        }

        $template = file_get_contents($stubPath);

        // Replace placeholders with actual model name & uploadPath
        $template = str_replace('{{modelName}}', $name, $template);
        $template = str_replace('{{uploadPath}}', Str::plural(strtolower($name)), $template);

        return $template;
    }

    private function addRoutesToApiFile($routeContent)
    {
        $apiFilePath = base_path('routes/api.php');

        // Check if routes/api.php exists, then append the new route
        if (File::exists($apiFilePath)) {
            File::append($apiFilePath, PHP_EOL . $routeContent);
        } else {
            $this->error("routes/api.php file does not exist.");
        }
    }

}
