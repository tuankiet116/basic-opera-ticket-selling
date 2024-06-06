<?php

namespace App\Services\Admin;

use App\Models\FileModel;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function getListFiles()
    {
        return FileModel::orderBy("created_at", "desc")->paginate(PAGINATE_NUMBER)->toArray();
    }

    public function deleteFile(int $fileId)
    {
        $fileRecord = FileModel::find($fileId);
        $file = Storage::exists("reports/$fileRecord->file_name.xlsx");
        if ($file) unlink(storage_path() . "/app/reports/$fileRecord->file_name.xlsx");
        return FileModel::find($fileId)->delete();
    }
}
