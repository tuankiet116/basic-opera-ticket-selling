<?php

namespace App\Services\Admin;

use App\Models\FileModel;

class FileService
{
    public function getListFiles()
    {
        return FileModel::orderBy("created_at", "desc")->paginate(PAGINATE_NUMBER)->toArray();
    }

    public function deleteFile(int $fileId)
    {
        return FileModel::find($fileId)->delete();
    }
}
