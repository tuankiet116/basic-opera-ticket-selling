<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

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
        return $this->responseSuccess($result);
    }

    public function download($fileName)
    {
        $file = File::get("/app/reports/$fileName.xlsx");
        return response()->download($file);
    }
}
