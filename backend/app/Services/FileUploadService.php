<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\IzinBukti;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public Izin $izin;
    public $file;
    public $filename;
    public $disk;
    public $path;
    public $extension;

    public function __construct($file = null, $filename = null, $disk = 'public', $path = 'izin/', $extension = null)
    {
        $this->file = $file;
        $this->filename = $filename;
        $this->disk = $disk;
        $this->path = $path;
        $this->extension = $extension;
    }

    public function upload()
    {
        $directory = Storage::disk($this->disk)->path($this->path);

        if (File::ensureDirectoryExists($directory)) {
            $directory = File::makeDirectory($directory);
        }

        Storage::disk($this->disk)->putFileAs($this->path, $this->file, $this->filename);

        $data = [
            'id_izin' => $this->izin->id,
            'nama' => $this->filename,
            'path' => $this->path,
            'ext' => $this->extension,
            'disk' => $this->disk,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        return $this->insertDB($data);
    }

    public function insertDB($payload)
    {
        return IzinBukti::create($payload);
    }

    /**
     * Set the Izin of file
     */
    public function setIzin(Izin $izin): self
    {
        $this->izin = $izin;

        return $this;
    }
    /**
     * Set the value of file
     */
    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Set the value of filename
     */
    public function setFilename($filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Set the value of disk
     */
    public function setDisk($disk): self
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Set the value of path
     */
    public function setPath($path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Set the value of extension
     */
    public function setExtension($extension): self
    {
        $this->extension = $extension;

        return $this;
    }
}
