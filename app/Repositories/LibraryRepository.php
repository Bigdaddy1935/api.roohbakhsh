<?php

namespace App\Repositories;

use App\Interfaces\LibraryRepositoryInterface;
use App\Models\Library;
use Illuminate\Support\Facades\Storage;

class LibraryRepository extends Repository implements LibraryRepositoryInterface
{

    public function model()
    {
        return Library::class;
    }
    public function upload($file)
    {
        $filenamewithextension = $file->getClientOriginalName();

        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

        //get file extension
        $extension = $file->getClientOriginalExtension();

        //filename to store
        $filenametostore = $filename.'_'.uniqid().'.'.$extension;

        //Upload File to external server
        Storage::disk('ftp')->put($filenametostore, fopen($file, 'r+'));

        //Store $filenametostore in the database
        return Storage::disk('ftp')->url('https://dl.poshtybanman.ir/upload/'.$filenametostore);
    }

}