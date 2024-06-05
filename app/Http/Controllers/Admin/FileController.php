<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\FileService;

class FileController extends Controller
{
    public function __construct(protected FileService $fileService)
    {
    }

    public function index()
    {
        $result = $this->fileService->getListFiles();
        return $this->responseSuccess($result);
    }

    public function delete(int $fileId)
    {
        $result = $this->fileService->deleteFile($fileId);
        return $this->responseSuccess([$result]);
    }

    public function download($fileName)
    {
        return response()->download(storage_path() . "/app/reports/$fileName.xlsx");
    }
}
