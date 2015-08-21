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
        
        $header = $mapping->mappingname;

        // select only selected columns only
        $datasetdata = DB::connection('dataset')->table($tablename)->select($header)->get();

        if (Session::get('aggregate')) {
            // aggregate dataset
            foreach ($header as $attribute) {
                // check if there is an attribute that contain aggregation information
                $categoryData = $dataset->categories()->where('name', $attribute)->first();
                if ($categoryData != null) {
                    $categories = DB::connection('dataset')->table($tablename)->select($attribute)->distinct()->get();
                    $rowsData = [];
                    foreach ($categories as $category) {
                        $rows = DB::connection('dataset')->table($tablename)->where($attribute, $category->$attribute)->get();
                        $rowData = (object) [];
                        // initialize rowdata
                        foreach ($header as $att) {
                            $rowData->$att = 0;
                        }
                        foreach ($rows as $row) {
                            foreach ($header as $att) {
                                if ($att == $attribute) {
                                    $rowData->$att = $row->$att;
                                } else {
                                    if (is_numeric($row->$att)) {
                                        $rowData->$att += $row->$att;
                                        //var_dump($row->$att);
                                    } else {
                                        $rowData->$att = $row->$att;
                                    }
                                }
                            }
                        }
                        //exit();
                        $rowsData[] = $rowData;
                        if ($categoryData->type == "AVERAGE") {
                            foreach ($header as $att) {
                                if ($att != $attribute) {
                                    if (is_numeric($row->$att)) {
                                        $rowData->$att /= count($rows);
                                    }
                                }
                            }
                        }
                    }
                    $datasetdata = $rowsData;
                    break;
                }
            }
        }

        // join dataset with header
        $data[] = $header;
        foreach ($datasetdata as $set) {
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
            'header' => $header,
            'data' => $data
        ];

        Session::put('visdata', (object) $visdata);

        // save user-visualization relation
        $user = Auth::user();
        $visualization = \App\Visualization::where('name', Session::get('visdata')->visualization)->first();
        $relation = $user->visualizations()->where('visualization_id', $visualization->id)->first();
        if ($relation != null){
            $relation->pivot->count += 1;
            $relation->pivot->save();
            $relation->save();
            //var_dump($relation->pivot->count);exit();
        } else {
            $user->visualizations()->attach($visualization, ['count' => 1]);
        }

        if ($relation->pivot->count == 3) {
            // check if already has rating
            $userrating = $user->ratings()->where('visualization_id', $visualization->id)->first();

            if ($userrating == null) {
                // set rating to 0.75 (white list)
                $rating = new \App\Rating();
                $rating->rating = 0.75;
                $rating->visualization_id = $visualization->id;
                $user->ratings()->save($rating);
            } else {
                if ($userrating->rating < 0.75) {
                    $userrating->rating = 0.75;
                    $userrating->save();
                }
            }
        }

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

    public function getSave() {
        // save configuration
        $user = Auth::user();

        $projectid = Session::get('visualizationid');
        if ($projectid != null) {
            $project = $user->projects()->where('id', $projectid)->first();
            $project->configuration = serialize(Session::get('visdata'));
            $project->save();
        }
        return redirect('setup');
    }

    public function postRating() {
        // save rating
        $user = Auth::user();
        $visualization = \App\Visualization::where('name', Session::get('visdata')['visualization'])->first();

        $userrating = $user->ratings()->where('visualization_id', $visualization->id)->first();

        if (Input::get('isPositive')) {
            if ($userrating == null) {
                // set rating to 0.75 (white list)
                $rating = new \App\Rating();
                $rating->rating = 1;
                $rating->visualization_id = $visualization->id;
                $user->ratings()->save($rating);
            } else {
                $userrating->rating = 1;
                $userrating->save();
            }   
        } else {
            if ($userrating == null) {
                // set rating to 0.75 (white list)
                $rating = new \App\Rating();
                $rating->rating = 0;
                $rating->visualization_id = $visualization->id;
                $user->ratings()->save($rating);
            } else {
                $userrating->rating = 0;
                $userrating->save();
            }
        }
    }
}
