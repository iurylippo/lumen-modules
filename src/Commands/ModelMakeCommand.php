<?php

namespace Nwidart\Modules\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends GeneratorCommand
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
    protected $name = 'module:make-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model for the specified module.';

    public function handle()
    {
        parent::handle();

        $this->handleOptionalMigrationOption();
        $this->handleOptionalWithAllOption();
    }

    /**
     * Create a proper migration name:
     * ProductDetail: product_details
     * Product: products
     * @return string
     */
    private function createMigrationName()
    {
        $pieces = preg_split('/(?=[A-Z])/', $this->argument('model'), -1, PREG_SPLIT_NO_EMPTY);

        $string = '';
        foreach ($pieces as $i => $piece) {
            if ($i+1 < count($pieces)) {
                $string .= strtolower($piece) . '_';
            } else {
                $string .= Str::plural(strtolower($piece));
            }
        }

        return $string;
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
            ['fillable', null, InputOption::VALUE_OPTIONAL, 'The fillable attributes.', null],
            ['migration', 'm', InputOption::VALUE_NONE, 'Flag to create associated migrations', null],
            ['withall', 'w', InputOption::VALUE_NONE, 'Flag to create all structure automatically for lumen', null],
        ];
    }

    /**
     * Create the migration file with the given model if migration flag was used
     */
    private function handleOptionalMigrationOption()
    {
        if ($this->option('migration') === true) {
            $migrationName = 'create_' . $this->createMigrationName() . '_table';
            $this->call('module:make-migration', ['name' => $migrationName, 'module' => $this->argument('module')]);
        }
    }

    private function handleOptionalWithAllOption()
    {
        if ($this->option('withall') === true) {

            $migrationName = 'create_' . $this->createMigrationName() . '_table';
            $this->call('module:make-migration', [
                'name' => $migrationName, 'module' => $this->argument('module')
            ]);

            $this->call('module:make-controller', [
                'controller' => $this->getModelName() . 'Controller',
                'module' => $this->getModuleName(),
            ]);

            $this->call('module:make-model-routes', [
                'model' => $this->getModelName(),
                'module' => $this->getModuleName(),
            ]);

            $this->call('module:make-service', [
                'service' => $this->getModelName(),
                'module' => $this->getModuleName(),
            ]);

            $this->call('module:make-model-contract', [
                'contract' => $this->getModelName(),
                'module' => $this->getModuleName(),
            ]);

            $this->call('module:make-repository', [
                'repository' => $this->getModelName(),
                'module' => $this->getModuleName(),
            ]);

            $this->call('module:make-test', [
                'name' => $this->getModelName().'Test',
                'module' => $this->getModuleName(),
            ]);

            $this->call('module:make-seed', [
                'name' => $this->getModelName(),
                'module' => $this->getModuleName(),
                '--master' => false,
            ]);

            $this->call('module:make-factory', [
                'name' => $this->getModelName(),
                'module' => $this->getModuleName(),
            ]);
        }
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub('/model.stub', [
            'NAME'              => $this->getModelName(),
            'FILLABLE'          => $this->getFillable(),
            'NAMESPACE'         => $this->getClassNamespace($module),
            'CLASS'             => $this->getClass(),
            'LOWER_NAME'        => $module->getLowerName(),
            'MODULE'            => $this->getModuleName(),
            'STUDLY_NAME'       => $module->getStudlyName(),
            'MODULE_NAMESPACE'  => $this->laravel['modules']->config('namespace'),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $modelPath = GenerateConfigReader::read('model');

        return $path . $modelPath->getPath() . '/' . $this->getModelName() . '.php';
    }

    /**
     * @return mixed|string
     */
    private function getModelName()
    {
        return Str::studly($this->argument('model'));
    }

    /**
     * @return string
     */
    private function getFillable()
    {
        $fillable = $this->option('fillable');

        if (!is_null($fillable)) {
            $arrays = explode(',', $fillable);

            return json_encode($arrays);
        }

        return '[]';
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace() : string
    {
        $module = $this->laravel['modules'];

        return $module->config('paths.generator.model.namespace') ?: $module->config('paths.generator.model.path', 'Entities');
    }
}
