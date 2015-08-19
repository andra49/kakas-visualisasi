<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use DB;
use Session;
use Auth;

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
        // foreach ($mapping->mapping as $columnid) {
        //     $header[] = $attributes[$columnid]->name;
        // }
        $header = $mapping->mappingname;

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

        $category = null; // save which data/column is used as category

        for ($i=0; $i < count($visualization->visualVariables); $i++) { 
            if($visualization->visualVariables[$i]->pivot->type == "category"){
                $category = $header[$i];
            }
            $test[] = [$i, $visualization->visualVariables[$i]->pivot->type];
        }
        //return response()->json($test);

        $visdata = [
            'visualization' => $visualization->name,
            'category' => $category,
            'data' => $data
        ];

        Session::put('visdata', (object) $visdata);

        // save configuration
        $user = Auth::user();
        $projectid = Session::get('visualizationid');

        $project = $user->projects()->where('id', $projectid)->first();
        $project->configuration = serialize($visdata);
        $project->save();

        // Call the view and then wait for data request
        return view('visualization.view');
    }

    public function getData() {
        return response()->json(Session::get('visdata'));
    }

    public function getLoad($id) {
        // load configuration
        $user = Auth::user();

        $project = $user->projects()->where('id', $id)->first();
        Session::put('visdata', unserialize($project->configuration));

        return view('visualization.view');
    }
}
