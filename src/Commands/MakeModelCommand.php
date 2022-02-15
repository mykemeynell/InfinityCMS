<?php

namespace Infinity\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeModelCommand extends ModelMakeCommand
{
    protected $name = 'infinity:make:model';
    protected $description = 'Creates a new Infinity model';

    /**
     * @inheritDoc
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/model.stub';
    }
}
