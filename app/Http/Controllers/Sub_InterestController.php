<?php

namespace App\Http\Controllers;

use App\Sub_interest;
use Illuminate\Http\Request;

class Sub_InterestController extends Controller
{
    public function getAll()
    {
        $sub_interests = Sub_interest::all();
        return response()->json(compact('sub_interests'));
    }
}
