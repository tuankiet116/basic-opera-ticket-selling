<?php

namespace App\Http\Controllers;

use App\Services\Admin\FileService;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    public function __construct(protected FileService $fileService)
    {
    }

    public function listFile()
    {
        $result = $this->fileService->getListFiles();
        return $this->responseSuccess($result);
    }

    public function deleteFile(string $fileName)
    {
        $result = $this->fileService->deleteFile($fileName);
        return $this->responseSuccess($result);
    }

    public function downloadFile(string $fileName)
    {
        $file = File::get("/app/reports/$fileName");
        return response()->download($file);
    }
}
