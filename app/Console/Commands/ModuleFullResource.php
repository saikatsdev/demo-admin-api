<?php

namespace App\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleFullResource extends Command
{
    protected $signature   = 'module:make-full-resource {name} {module} {--backend} {--frontend}';
    protected $description = 'Generate a resource inside a specified module with Model, Migration, Controller, Request, Resource Collection, and Repository';

    public function handle()
    {
        $moduleName   = ucfirst($this->argument('module')); // Module Name (e.g., Product)
        $resourceName = ucfirst($this->argument('name'));   // Resource Name (e.g., Category, Brand)
        $type         = $this->option('backend') ? 'Backend' : 'Frontend';

        // Directory for the module
        $moduleDirectory = base_path("Modules/{$moduleName}");

        // Check if the module exists
        if (!File::exists($moduleDirectory)) {
            $this->error("Module {$moduleName} does not exist.");
            return;
        }

        // Ensure required directories exist
        $this->ensureDirectoryExists($moduleDirectory . "/Entities");
        $this->ensureDirectoryExists($moduleDirectory . "/Http/Controllers/{$type}");
        $this->ensureDirectoryExists($moduleDirectory . "/Http/Requests/{$type}");
        $this->ensureDirectoryExists($moduleDirectory . "/Transformers/{$type}");
        $this->ensureDirectoryExists($moduleDirectory . "/Repositories");
        $this->ensureDirectoryExists($moduleDirectory . "/Database/Migrations");

        // Create Model
        $this->createFileFromStub('model.stub', $moduleDirectory . "/Entities/{$resourceName}.php", [
            '{{moduleName}}' => $moduleName,
            '{{modelName}}'  => $resourceName,
            '{{namespace}}'  => "Modules\\{$moduleName}\\Entities",
            '{{uploadPath}}' => Str::plural(strtolower($resourceName)),
        ]);

        // Create Controller
        $this->createFileFromStub('controller.stub', $moduleDirectory . "/Http/Controllers/{$type}/{$resourceName}Controller.php", [
            '{{moduleName}}' => $moduleName,
            '{{model}}'      => $resourceName,
            '{{type}}'       => $type,
            '{{modelSnake}}' => Str::snake($resourceName),
            '{{modelCamel}}' => Str::camel($resourceName),
        ]);

        // Create Request
        $this->createFileFromStub('request.stub', $moduleDirectory . "/Http/Requests/{$type}/{$resourceName}Request.php", [
            '{{moduleName}}'  => $moduleName,
            '{{requestName}}' => $resourceName,
            '{{type}}'        => $type,
        ]);

        // Create Resource
        $this->createFileFromStub('resource.stub', $moduleDirectory . "/Transformers/{$type}/{$resourceName}Resource.php", [
            '{{moduleName}}'   => $moduleName,
            '{{resourceName}}' => $resourceName,
            '{{type}}'         => $type,
        ]);

        // Create Collection
        $this->createFileFromStub('collection.stub', $moduleDirectory . "/Transformers/{$type}/{$resourceName}Collection.php", [
            '{{moduleName}}'     => $moduleName,
            '{{collectionName}}' => $resourceName,
            '{{type}}'           => $type,
        ]);

        // Create Migration
        $this->createFileFromStub('migration.stub', $moduleDirectory . "/Database/Migrations/" . date('Y_m_d_His') . '_create_' . Str::snake(Str::plural($resourceName)) . '_table.php', [
            '{{moduleName}}' => Str::plural($resourceName),
            '{{modelName}}'  => $resourceName,
            '{{tableName}}'  => Str::snake(Str::plural($resourceName)),
        ]);

        // Create Repository
        $this->createFileFromStub('repository.stub', $moduleDirectory . "/Repositories/{$resourceName}Repository.php", [
            '{{moduleName}}'      => $moduleName,
            '{{repositoryName}}'  => $resourceName,
            '{{repositoryCamel}}' => Str::camel($resourceName),
        ]);
    }

    private function ensureDirectoryExists($directory)
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }
    }

    private function createFileFromStub($stubName, $filePath, $replacements)
    {
        if (!File::exists($filePath)) {
            $stubPath = app_path("Console/Stubs/Module/{$stubName}");

            if (!File::exists($stubPath)) {
                throw new \Exception("{$stubName} stub file not found!");
            }

            $stubContent = file_get_contents($stubPath);

            foreach ($replacements as $placeholder => $replacement) {
                $stubContent = str_replace($placeholder, $replacement, $stubContent);
            }

            File::put($filePath, $stubContent);
            $this->info(basename($filePath) . " created successfully!");
        }
    }
}
