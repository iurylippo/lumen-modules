<?php

namespace Nwidart\Modules\Commands;

use App\Services\Utils;
use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

class ContractMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'contract';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-model-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model contract for the specified module.';

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
            ['contract', InputArgument::REQUIRED, 'The name of contract will be created.'],
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

        return (new Stub('/repository-contract.stub', [
            'CONTRACT_NAMESPACE'    => $this->getClassNamespace($module),
            'CONTRACT_NAME'         => $this->getContractName(),
        ]))->render();
    }

    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.contracts.namespace') ?: $module->config('paths.generator.contracts.path', 'Repositories/Contracts');
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $contractsPath = GenerateConfigReader::read('contracts');
        return $path . $contractsPath->getPath() . '/' . $this->getContractName() .  '.php';
    }

    /**
     * @return mixed|string
     */
    private function getContractName()
    {
        $contract = Str::studly($this->argument('contract'));

        if (Str::contains(strtolower($contract), 'contract') === false) {
            $contract .= 'RepositoryContract';
        }
        return $contract;
    }

     /**
     * @return mixed|string
     */
    private function getStudlyName()
    {
        return Str::studly($this->argument('contract'));
    }

    /**
     * @return mixed|string
     */
    private function getLowerName()
    {
        return Str::lower($this->argument('contract'));
    }
}
