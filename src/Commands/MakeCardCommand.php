<?php

namespace Infinity\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeCardCommand extends GeneratorCommand
{
    protected $signature = 'infinity:make:card {name}';
    protected $description = 'Create a new Infinity card.';

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/card.stub';
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Cards';
    }

    /**
     * @inheritDoc
     */
    protected function getOptions(): array
    {
        return [
            ['view', null, InputOption::VALUE_NONE, 'View name', null],
        ];
    }
}
