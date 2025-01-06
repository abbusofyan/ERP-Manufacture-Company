<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Process;

class ProcessController extends Controller
{
    public function getByDepartment($department) {
        $processes = Process::with('detail')->where('type', $department)->get();
        return $processes;
    }
}
