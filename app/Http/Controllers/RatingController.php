<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User as User;
use App\Visualization as Visualization;
use App\Dataset as Dataset;
use App\Attribute as Attribute;
use Input;
use Session;

class RatingController extends Controller
{
     /**
     * Responds to requests to GET /rating
     */
    public function getIndex()
    {
        $dataset = Dataset::all(['id', 'table_name']);
        $names = [];
        foreach ($dataset as $table_name) {
            $names[] = $table_name->table_name;
        }

        return view('rating.selectdataset', [
            'datasets' => $dataset
        ]);
    }

    /*
    *   TODO : Change to a more generic approach
    */
    public function mapper($numData, $numVisual)
    {
        // limited to 3 visual variables
        $mappings = [];

        for ($i=0; $i < $numData; $i++) {
            if ($numVisual == 1) {
                $mappings[] = [$i];
            } else {
                for ($j=0; $j < $numData; $j++) { 
                    if ($i != $j) {
                        if ($numVisual == 2) {
                            $mappings[] = [$i, $j];
                        } else {
                            for ($k=0; $k < $numData; $k++) { 
                                if ($i != $k && $j != $k) {
                                    if ($numVisual == 3) {
                                        $mappings[] = [$i, $j, $k];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $mappings;
    }


    public function matcher($dataset, $visualization)
    {
        // get number of data and visual variables
        $numData = count($dataset->attributes);
        $numVisual = count($visualization->visualVariables);

        // generate mappings
        $mappings = $this->mapper($numData, $numVisual);

        return $mappings;
    }


    /*
    *   TODO : Change to a more generic approach
    *   TODO : Add other rating factors (user-shared, user and device information)
    */
    public function getRating()
    {
        // load selected dataset
        $dataset = Dataset::findOrFail(Input::get('dataset'));

        $rating = [];

        foreach (Visualization::all() as $visualization) {
            // GENERATE MAPPINGS
            $mappings = $this->matcher($dataset, $visualization); // [0, 1], [0,2], ...
            $mappingsRating;
            $bestrating = 0;

            // GENERATE RATING FOR EACH MAPPING
            foreach ($mappings as $map) {
                // FACTUAL VISUALIZATION KNOWLEDGE
                $mapRating = $this->factualVisualizationKnowledge($visualization, $dataset, $map);

                // select only the best mapping for each visualization
                if ($mapRating > $bestrating){
                    $mappingsRating = (object) ['rating' => $mapRating, 'mapping' => $map];
                    $bestrating = $mapRating;
                }
            }
            $visrating = (object) ['visualizationid' => $visualization->id, 
                'rating' => $mappingsRating->rating, 
                'mapping' => $mappingsRating->mapping,
                'datasetid' => $dataset->id
            ];
            $rating[] = $visrating;
        }

        // sort mapping
        usort($rating, array("App\Http\Controllers\RatingController", "cmp"));

        // Save mappings to session
        Session::put('mappings', $rating);

        return view('rating.selectvisualization', [
            'configuration' => $rating
        ]);

        //return response()->json($rating);
    }

    private function factualVisualizationKnowledge($visualization, $dataset, $map) {
        // get visual variables
        $visualType = $visualization->visualVariables;

        // get data types
        $dataType = $dataset->attributes;

        $numVisualVariables = count($map);

        // Mapping rating
        $mapRating = 0;

        for ($i=0; $i < $numVisualVariables; $i++) { 
            $type = $dataType[$map[$i]]->data_variable_type;
            if ($type == 'nominal') {
                $mapRating += $visualType[$i]->nominal_rating;
            } else if ($type == 'ordinal') {
                $mapRating += $visualType[$i]->ordinal_rating;
            } else if ($type == 'kuantitatif') {
                $mapRating += $visualType[$i]->quantitative_rating;
            } else {
                // Error
            }
        }
        $mapRating /= $numVisualVariables;
        // set maximum value to 100
        $mapRating *= 10/13;

        return $mapRating;
    }

    // sorter function
    public static function cmp($a, $b) {
       return $b->rating - $a->rating;
    }
}
