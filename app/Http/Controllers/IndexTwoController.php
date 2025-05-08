<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexTwoController extends Controller
{
    public function viewIndexTwo()
    {
        return view('calendar.indexTwo');
    }
}
