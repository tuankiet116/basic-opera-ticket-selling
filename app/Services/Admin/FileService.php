<?php

namespace App\Services\Admin;

use App\Models\FileModel;

class FileService
{
    public function getListFiles()
    {
        return FileModel::orderBy("created_at", "DESC")->paginate(PAGINATE_NUMBER)->toArray();
    }

    public function deleteFile(string $fileName)
    {
        return FileModel::where("file_name", $fileName)->delete();
    }
}
