<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: "make:mitd")]
class MITDMakeCommand extends GeneratorCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "make:mitd {name : The name of the class}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Create a new class with the MITD namespace";

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = "MITD";

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub() {
        return $this->resolveStubPath("/stubs/mitd.stub");
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub) {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, "/")))
            ? $customPath
            : __DIR__ . $stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) {
        return $rootNamespace . "\MITD";
    }
}
