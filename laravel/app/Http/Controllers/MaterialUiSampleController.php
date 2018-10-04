<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialUiSampleController extends Controller
{
    public function react()
    {
        return view('material_ui_sample');
    }
}
