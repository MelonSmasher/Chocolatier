<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\App;
use App\Http\Requests\Request;
use App\Model\User;

abstract class Controller extends BaseController
{
    use DispatchesJobs, ValidatesRequests;
}
