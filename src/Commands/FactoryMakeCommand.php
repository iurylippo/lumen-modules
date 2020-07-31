<?php

namespace Nwidart\Modules\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class FactoryMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-factory';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model factory for the specified module.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the factory.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $model_namespace = $this->getDefaultModelNamespace() . "\\" . $this->getModelForFactory();
        return (new Stub('/factory.stub', [
            'MODEL_NAMASPACE', $model_namespace
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $factoryPath = GenerateConfigReader::read('factory');

        return $path . $factoryPath->getPath() . '/' . $this->getFileName();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        $factory = Str::studly($this->argument('name'));
        if (Str::contains(strtolower($factory), 'factory') === false) {
            $factory .= 'Factory';
        }
        $factory .= '.php';
        return $factory ;
    }

    /**
     * @return string
     */
    private function getModelForFactory()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * @return string
     */
    private function getDefaultModelNamespace() : string
    {
        $module = $this->laravel['modules'];

        $namespace = $module->config('namespace');

        $namespace .= '\\' . $this->getModuleName();

        $defaultRepositoryNameSpace = $module->config('paths.generator.model.namespace') ?:
                                      $module->config('paths.generator.model.path', 'Entities');

        $namespace .= '\\' . $defaultRepositoryNameSpace;

        $namespace = str_replace('/', '\\', $namespace);

        return $namespace;
    }
}
