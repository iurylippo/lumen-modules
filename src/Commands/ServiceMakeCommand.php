<?php

namespace Nwidart\Modules\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

class ServiceMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'service';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service for the specified module.';

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
            ['service', InputArgument::REQUIRED, 'The name of service will be created.'],
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

        return (new Stub('/service.stub', [
            'SERVICE_NAMESPACE'     => $this->getClassNamespace($module),
            'SERVICE_CLASS'         => $this->getServiceName(),
            'REPOSITORY_NAMESPACE'  => $this->getDefaultRepositoryContractNamespace(),
            'SERVICENAME_STUDLY'    => $this->getStudlyName(),
            'MODEL_NAMESPACE'       => $this->getDefaultModelNamespace()
        ]))->render();
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.services.namespace') ?: $module->config('paths.generator.services.path', 'Services');
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

        $servicesPath = GenerateConfigReader::read('services');
        return $path . $servicesPath->getPath() . '/' . $this->getServiceName() .  '.php';
    }

    /**
     * @return mixed|string
     */
    private function getServiceName()
    {
        $service = Str::studly($this->argument('service'));

        if (Str::contains(strtolower($service), 'service') === false) {
            $service .= 'Service';
        }
        return $service;
    }

     /**
     * @return mixed|string
     */
    private function getStudlyName()
    {
        return Str::studly($this->argument('service'));
    }

    /**
     * @return mixed|string
     */
    private function getLowerName()
    {
        return Str::lower($this->argument('service'));
    }
}
