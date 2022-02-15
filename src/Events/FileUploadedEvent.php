<?php

namespace Infinity\Events;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpFoundation\File\File;

class FileUploadedEvent implements ShouldQueue
{
    use Dispatchable, SerializesModels;

    public File $uploadedFile;

    /**
     * Create a new event instance.
     *
     * @param \Symfony\Component\HttpFoundation\File\File $uploadedFile
     */
    public function __construct(File $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }
}
