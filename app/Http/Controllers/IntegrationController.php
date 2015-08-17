<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use DB;
use Session;

class IntegrationController extends Controller
{
     public function getIndex()
    {
        //return response()->json(Session::get('mappings'));
        // get selected mapping
        $mappingid = Input::get('mappingid');
        $mappings = Session::get('mappings');
        $mapping = $mappings[$mappingid];


        // get selected columns name
        $header = [];
        $dataset = \App\Dataset::findOrFail($mapping->datasetid);
        $tablename = $dataset->table_name;
        $attributes = $dataset->attributes()->get();
        foreach ($mapping->mapping as $columnid) {
            $header[] = $attributes[$columnid]->name;
        }


        // select only selected columns only
        $dataset = DB::connection('dataset')->table($tablename)->select($header)->get();

        // join dataset with header
        $data[] = $header;
        foreach ($dataset as $set) {
            $row = [];
            foreach ($set as $value) {
                $row[] = $value;
            }
            $data[] = $row;
        }

        // get visualization visual variable information
        $visualization = \App\Visualization::findOrFail($mapping->visualizationid);

        $category; // save which data/column is used as category

        //return response()->json($visualization->visualVariables);
        for ($i=0; $i < count($visualization->visualVariables); $i++) { 
            if($visualization->visualVariables[$i]->pivot->type == "category"){
                $category = $header[$i];
            }
        }

        Session::put('visdata', (object)[
            'category' => $category,
            'data' => $data
        ]);

        // Call the view and then wait for data request
        return view('visualization.view');
    }

    public function getData() {
        return response()->json(Session::get('visdata'));
    }
}
