<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importExcel()

    {

        if(Input::hasFile('import_file')){

            $path = Input::file('import_file')->getRealPath();

            $data = Excel::load($path, function($reader) {

            })->get();


            if(!empty($data) && $data->count()){

                foreach ($data as $key => $value) {

                    foreach ($value as $key1 => $v) {
                        //dd($v->name);
                        $insert[] = ['role' => 'salon'];
                    }
                }

                if(!empty($insert)){

                    DB::table('users')->insert($insert);

                    //dd('Insert Record successfully.');

                }

            }

        }

        return response()->json(['success']);

    }
}
