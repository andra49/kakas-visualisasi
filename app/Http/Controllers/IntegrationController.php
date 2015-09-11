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

        // get project
        $project = \App\VisualizationProject::findOrFail(Session::get('visualizationid'));

        // select only selected columns only
        $datasetdata = DB::connection('dataset')->table($tablename)->select($header);
        $sortdata = null;

        foreach ($project->dataSelections as $selection) {
            if ($selection->operand == "##SORTBY##") {
                $sortdata = $selection;
            } else {
                $datasetdata = $datasetdata->where($selection->column_name, $selection->operator, $selection->operand);
            }
        }

        if ($sortdata !== null) {
            $sorttype = ($sortdata->operator == '>') ? 'asc' : 'desc';
            $datasetdata = $datasetdata->orderBy($sortdata->column_name, $sorttype);
        }

        $datasetdata = $datasetdata->get();


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

        $category = []; // save which data/column is used as category

        for ($i=0; $i < count($visualization->visualVariables); $i++) { 
            if($visualization->visualVariables[$i]->pivot->type == "category"){
                $category[] = $header[$i];
            }
            $test[] = [$i, $visualization->visualVariables[$i]->pivot->type];
        }
        //return response()->json($test);

        $visdata = [
            'visualization' => $visualization->name,
            'category' => $category,
            'header' => $header,
            'data' => $data,
            'activities' => $visualization->activities
        ];

        Session::put('visdata', (object) $visdata);

        // Call the view and then wait for data request
        return view('visualization.view', [
            'projectname' => $project->name
        ]);
    }

    public function getData() {
        return response()->json(Session::get('visdata'));
    }

    public function getLoad($id) {
        // load configuration
        $user = Auth::user();

        $project = $user->projects()->where('id', $id)->first();
        Session::put('visdata', unserialize($project->configuration));

        return view('visualization.view', [
            'projectname' => $project->name
        ]);
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

        // save user-visualization relation
        $visualization = \App\Visualization::where('name', Session::get('visdata')->visualization)->first();
        $relation = $user->visualizations()->where('visualization_id', $visualization->id)->first();
        if ($relation != null){
            $relation->pivot->count += 1;
            $relation->pivot->save();
            $relation->save();

            if ($relation->pivot->count == 3) {
                // check if already has rating
                $userrating = $relation->pivot->rating;
                if ($userrating == null) {
                    $relation->pivot->rating = 0.75;
                    $relation->pivot->save();
                    $relation->save();
                }
            }
        } else {
            $user->visualizations()->attach($visualization, ['count' => 1, 'rating' => null]);
        }

        return redirect('setup');
    }

    public function postFeedback() {
        $treshold = 5;
        $time = Input::get('time');

        // save rating
        $user = Auth::user();
        $visualization = \App\Visualization::where('name', Session::get('visdata')->visualization)->first();
        $relation = $user->visualizations()->where('visualization_id', $visualization->id)->first();

        if ($time < $treshold) {
            // check if already has rating
            if ($relation->pivot->rating === null) {
                $relation->pivot->rating = 0.25;
                $relation->pivot->save();
                $relation->save();
            }
        }

        return redirect('setup/rating');
    }

    public function postRating() {
        // save rating
        $user = Auth::user();
        $visualization = \App\Visualization::where('name', Session::get('visdata')->visualization)->first();
        $relation = $user->visualizations()->where('visualization_id', $visualization->id)->first();

        if (Input::get('isPositive')) {
            // check if already has rating
            $relation->pivot->rating = 1;
            $relation->pivot->save();
            $relation->save();
        } else {
            $relation->pivot->rating = 0;
            $relation->pivot->save();
            $relation->save();
        }
    }
}
