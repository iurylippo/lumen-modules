<?php

namespace Nwidart\Modules\Commands;

use Nwidart\Modules\Utils;
use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Throwable;

class ModelRoutesMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'model';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-model-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model routes for the specified module.';

    public function handle()
    {
        $path    = str_replace('\\', '/', $this->getDestinationFilePath());
        $content = $this->getTemplateContents();

        try {
            Utils::appendStringOnFile($path, 'Modelroutes', $this->getModuleName(), $content);
            $this->info("Created : {$path}.");
        } catch (Throwable $e) {
            $this->error("Error on creating model routes : {$path}.");
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['model', InputArgument::REQUIRED, 'The name of model will be created.'],
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
        return (new Stub('/model-routes.stub', [
            'LOWER_MODEL'       => $this->getLowerModelNamePlural(),
            'MODEL'             => $this->getModelName(),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $routesPath = GenerateConfigReader::read('routes');

        return $path . $routesPath->getPath() . '/web.php';
    }

    /**
     * @return mixed|string
     */
    private function getModelName()
    {
        return Str::studly($this->argument('model'));
    }

    /**
     * @return mixed|string
     */
    private function getLowerModelName()
    {
        return Str::lower($this->argument('model'));
    }

    /**
     * @return mixed|string
     */
    private function getLowerModelNamePlural()
    {
        return Str::plural($this->getLowerModelName());
    }
}
