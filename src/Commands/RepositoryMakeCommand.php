<?php

namespace Nwidart\Modules\Commands;

use Nwidart\Modules\Utils;
use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

class RepositoryMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'repository';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository for the specified module.';

    public function handle()
    {
        parent::handle();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['repository', InputArgument::REQUIRED, 'The name of repository will be created.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [

        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        $bindsContent = (new Stub('/provider-binds-register.stub', [
            'REPOSITORY_NAMESPACE'  => $this->getClassNamespace($module),
            'REPOSITORY_CLASS'      => $this->getRepositoryName(),
            'CONTRACTS_NAMESPACE'   => $this->getDefaultRepositoryContractNamespace(),
            'REPOSITORY_NAME'       => $this->getStudlyName()
        ]))->render();

        $moduleBindsProviderPath = \getenv('APP_PATH') . 'app/Providers/ModulesBindsProvider.php';
        $moduleBindsProviderPath = \str_replace("\\", "/", $moduleBindsProviderPath);
        Utils::appendStringOnFile($moduleBindsProviderPath, "modulesBinds", $this->getModuleName(), $bindsContent);

        return (new Stub('/repository.stub', [
            'REPOSITORY_NAMESPACE'  => $this->getClassNamespace($module),
            'REPOSITORY_CLASS'      => $this->getRepositoryName(),
            'MODEL_NAMESPACE'       => $this->getDefaultModelNamespace(),
            'CONTRACTS_NAMESPACE'   => $this->getDefaultRepositoryContractNamespace(),
            'REPOSITORY_NAME'       => $this->getStudlyName()
        ]))->render();
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.repository.namespace') ?: $module->config('paths.generator.repository.path', 'Repositories');
    }

    public function getDefaultRepositoryContractNamespace() : string
    {
        $module = $this->laravel['modules'];

        $namespace = $module->config('namespace');

        $namespace .= '\\' . $this->getModuleName();

        $defaultRepositoryNameSpace = $module->config('paths.generator.contracts.namespace') ?:
                                      $module->config('paths.generator.contracts.path', 'Repositories/Contracts');

        $namespace .= '\\' . $defaultRepositoryNameSpace;

        $namespace = str_replace('/', '\\', $namespace);

        return $namespace;
    }

    public function getDefaultModelNamespace() : string
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


    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $repositoriesPath = GenerateConfigReader::read('repository');
        return $path . $repositoriesPath->getPath() . '/' . $this->getRepositoryName() .  '.php';
    }

    /**
     * @return mixed|string
     */
    private function getRepositoryName()
    {
        $repository = Str::studly($this->argument('repository'));

        $repositoryDriver = $this->laravel['modules']->config('paths.generator.repository.driver');

        if (Str::contains(strtolower($repository), 'repository') === false) {
            $repository .= 'Repository' .  $repositoryDriver;
        }
        return $repository;
    }

     /**
     * @return mixed|string
     */
    private function getStudlyName()
    {
        return Str::studly($this->argument('repository'));
    }

    /**
     * @return mixed|string
     */
    private function getLowerName()
    {
        return Str::lower($this->argument('repository'));
    }
}
