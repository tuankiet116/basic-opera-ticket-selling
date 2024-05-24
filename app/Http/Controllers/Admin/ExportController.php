<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UserExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Excel;

class ExportController extends Controller
{
    public function __construct(protected Excel $excel)
    {
    }

    public function exportReport()
    {
        return $this->excel->download(new UserExport, "userExport.xlxs");
    }
}
