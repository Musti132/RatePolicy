<?php

namespace Musti\RatePolicy\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Pluralizer;

class RatePolicyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:rate-policy {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new Rate Policy';

    /**
     * Create a new command instance.
     * @param Filesystem $files
     */
    public function __construct(public Filesystem $fileSystem)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();

        if (!$this->fileSystem->exists($path)) {
            $this->fileSystem->put($path, $contents);
            $this->info("File : {$path} created");
        } else {
            $this->info("File : {$path} already exits");
        }
    }

    /**
     * Returns the class name of the file to be created
     * 
     * @return string
     */
    public function getClassName(bool $singularClassName = true): string
    {
        $name = $singularClassName ? $this->getSingularClassName($this->argument('name')) : $this->argument('name');

        if (strpos($this->argument('name'), '/') !== false) {
            $className = explode('/', $this->argument('name'));
            $name = $singularClassName ? $this->getSingularClassName(end($className)) : end($className);
        }

        return $name;
    }

    /**
     * Get the path of the file to be created and the name of the file
     * 
     * @return string
     */
    public function getPathAndFile(): string
    {
        $name = $this->getClassName();

        //Check if argument name contains a path
        if (strpos($this->argument('name'), '/') !== false) {
            //Get the path ignore everything after the last slash
            $path = substr($this->argument('name'), 0, strrpos($this->argument('name'), '/'));
            $name = $path . '/' . $name;
        }

        return $name;
    }

    /**
     * Return the namespace of the file to be created
     * 
     * @return string
     */
    public function getNamespace(): string
    {
        $namespace = 'App\RatePolicies';

        //Check if argument name contains a path and get the namespace
        if (strpos($this->argument('name'), '/') !== false) {
            $namespace = substr($this->argument('name'), 0, strrpos($this->argument('name'), '/'));
            $namespace = 'App\RatePolicies\\' . str_replace('/', '\\', $namespace);
        }

        return $namespace;
    }

    /**
     * Return the stub file path
     * 
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/RatePolicy.stub';
    }

    /**
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables()
    {
        $name = $this->getClassName();
        $namespace = $this->getNamespace();

        return [
            'CLASS_NAME'        => $name . 'RatePolicy',
            'CLASS_NAMESPACE'   => $namespace,
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     */
    public function getSourceFile()
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }


    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$' . $search . '$', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        return base_path('app/RatePolicies') . '/' . $this->getPathAndFile() . 'RatePolicy.php';
    }

    /**
     * Return the Singular Capitalize Name
     * @param $name
     * @return string
     */
    public function getSingularClassName($name)
    {
        return ucwords(Pluralizer::singular($name));
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!$this->fileSystem->isDirectory($path)) {
            $this->fileSystem->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }
}
