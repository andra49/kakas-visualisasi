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
use Schema;
use Auth;

class RatingController extends Controller
{
    private $combinations;
    private $mappings;
     /**
     * Responds to requests to GET /rating
     */
    public function getIndex()
    {
        // get current user data
        $user = Auth::user();

        // get available visualization
        $visualization = $user->projects->toArray();

        return view('rating.home', [
            'data' => $visualization,
            'columnnames' => Schema::getColumnListing('visualization_projects')
        ]);
    }

    public function getNew()
    {
        return view('rating.newvisualization', [
            'datasets' => Dataset::all()
        ]);
    }

    public function postNew()
    {
        if (Input::get('name') != null){
            $vis = new \App\VisualizationProject;
            $vis->name = Input::get('name');
            
            $user = Auth::user();
            $vis->user_id = $user->id;
            
            $dataset = Dataset::findOrFail(Input::get('datasetid'));
            $dataset->projects()->save($vis);
            Session::put('visualizationid', $vis->id);
            return redirect('dataset/selection/'.$vis->id);
        } else {
            return response()->json(['status' => 'failed']);            
        }
    }

    public function getVisualization($id)
    {
        Session::put('visualizationid', $id);
        return redirect('setup/rating');
    }


    private function combination($arr, $len, $startPosition, $result) 
    {
        if ($len == 0) {
            $newresult = [];
            // deep copy
            foreach ($result as $value) {
                $newresult[] = $value;
            }
            $this->combinations[] = $newresult;
        }
        for ($i = $startPosition; $i <= count($arr) - 1; $i++) { 
            $result[count($result) - $len] = $arr[$i];
            $this->combination($arr, $len - 1, $i + 1, $result);
        }
    }

    private function permutation($n, $arr)
    {
        if ($n == 1) {
            $newarr = [];
            // deep copy
            foreach ($arr as $value) {
                $newarr[] = $value;
            }
            $this->mappings[] = $newarr;
        } else {
            for ($i = 0; $i < $n - 1; $i++) { 
                $this->permutation($n - 1, $arr);
                if ($n % 2 == 0){ // even
                    $temp = $arr[$i];
                    $arr[$i] = $arr[$n - 1];
                    $arr[$n - 1] = $temp;
                } else {
                    $temp = $arr[0];
                    $arr[0] = $arr[$n - 1];
                    $arr[$n - 1] = $temp;
                }
            }
            $this->permutation($n - 1, $arr);
        }
    }

    public function mapper($numData, $numVisual)
    {
        // initialize data
        $availableData = [];
        for ($i=0; $i < $numData; $i++) { 
            $availableData[] = $i;
        }

        $availableVisual = [];
        for ($i=0; $i < $numVisual; $i++) { 
            $availableVisual[] = null;
        }

        $this->combination($availableData, $numVisual, 0, $availableVisual);
        foreach ($this->combinations as $combination) {
            $this->permutation($numVisual, $combination);
        }

        // deep copy
        $mappings = [];
        foreach ($this->mappings as $mapping) {
            $temp = [];
            foreach ($mapping as $pair) {
                $temp[] = $pair;
            }
            $mappings[] = $temp;
        }

        // reset current mappings
        $this->combinations = [];
        $this->mappings = [];

        return $mappings;
    }


    public function matcher($dataset, $visualization)
    {
        // get number of data and visual variables
        $numData = count($dataset);
        $numVisual = count($visualization->visualVariables);

        // generate mappings
        $mappings = $this->mapper($numData, $numVisual);

        return $mappings;
    }

    public function getRating()
    {
        if (Session::get('visualizationid') == null) {
            return redirect('setup');
        } else {
            // load project
            $project = \App\VisualizationProject::findOrFail(Session::get('visualizationid'));

            // load selected dataset
            $dataset = $project->dataset;

            return view('rating.selectvisualization', [
                'attributes' => $dataset->attributes
            ]);
            // return response()->json($dataset->attributes);
        }
    }

    public function postRecommendation()
    {
        if (Session::get('visualizationid') == null) {
            return redirect('setup');
        } else {
            // load project
            $project = \App\VisualizationProject::findOrFail(Session::get('visualizationid'));

            // load selected dataset
            $dataset = $project->dataset;

            // get attribute selection
            $selection = Input::get('selection');

            // is exact match?
            $isExact = Input::get('exact');

            // has purpose type?
            $purpose = Input::get('purpose');

            // on mobile?
            $isMobile = Input::get('mobile');

            // aggregate?
            $isAggregate = false;//Input::get('aggregate');
            Session::put('aggregate', $isAggregate);

            $rating = [];
            $dumpdata = [];

            foreach (Visualization::all() as $visualization) {
                // PRE SELECTION
                if ($visualization->purpose_type == $purpose || $purpose == 'ALL') {
                    if (count($selection) < count($visualization->visualVariables)) {
                        // if the selected attributes is less than the number of visual variables needed
                    } else {
                        if (!$isExact || (count($selection) == count($visualization->visualVariables))) {

                            // GENERATE MAPPINGS
                            $mappings = $this->matcher($selection, $visualization); // [0, 1], [0,2], ...
                            $mappingsRating = null;
                            $bestrating = 0;

                            // GENERATE RATING FOR EACH MAPPING
                            foreach ($mappings as $map) {
                                $mapRating = 0;
                                $numFactors = 3;

                                // FACTUAL VISUALIZATION KNOWLEDGE
                                $factual = $this->factualVisualizationKnowledge($visualization, $dataset, $selection, $map);
                                $mapRating += $factual;

                                // USER & DEVICE INFORMATION
                                $userdevice = $this->userDeviceInformation($visualization, $isMobile);
                                $mapRating += $userdevice;

                                // USER-SHARED KNOWLEDGE
                                $vis = Visualization::where('name', $visualization->name)->first();
                                $usersharedrating = $this->userSharedKnowledge($vis);
                                if ($usersharedrating !== null){
                                    $usershared = $usersharedrating * 100;
                                    $mapRating += $usershared;
                                } else {
                                    $numFactors--;
                                }

                                $mapRating /= $numFactors;

                                $temp[] = $map;
                                // select only the best mapping for each visualization
                                if ($mapRating > $bestrating){
                                    $mappingsRating = (object) ['rating' => $mapRating, 'mapping' => $map, 'scores' => [$factual, $userdevice, $usershared]];
                                    $bestrating = $mapRating;
                                }
                            }

                            $mappingname = [];
                            foreach ($mappingsRating->mapping as $mapping) {
                                $mappingname[] = $selection[$mapping];
                            }

                            $visrating = (object) ['visualizationid' => $visualization->id,
                                'visualization' => $visualization->name, 
                                'rating' => $mappingsRating->rating,
                                'scores' => $mappingsRating->scores,
                                'mapping' => $mappingsRating->mapping,
                                'mappingname' => $mappingname,
                                'datasetid' => $dataset->id
                            ];

                            // check if there is other version of visualization is used (barchart and 2-data barchart)
                            $exist = false;
                            for ($i = 0; $i < count($rating); $i++) {
                                if ($visrating->visualization == $rating[$i]->visualization) {
                                    if (count($visrating->mapping) >= count($rating[$i]->mapping)) {
                                        $rating[$i] = $visrating;
                                    }
                                    $exist = true;
                                    break;
                                }
                            }
                            if (!$exist){
                                $rating[] = $visrating;                
                            }
                        }
                    }
                }
            }

            // sort mapping
            usort($rating, array("App\Http\Controllers\RatingController", "cmp"));

            // Save mappings to session
            Session::put('mappings', $rating);

            return response()->json([
                'mappings' => $rating
            ]);
        }
    }

    private function factualVisualizationKnowledge($visualization, $dataset, $selection, $map) {
        // get visual variables
        $visualType = $visualization->visualVariables;

        // get attributes
        $dataType = $dataset->attributes;

        $numVisualVariables = count($map);

        // Mapping rating
        $mapRating = 0;

        for ($i=0; $i < $numVisualVariables; $i++) { 
            //$type = $dataType[$map[$i]]->data_variable_type;
            $name = $selection[$map[$i]];
            $type = $dataType->where('name', $name)->first()->data_variable_type;

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

    private function userSharedKnowledge($vis) {
        $user = Auth::user();
        $visualization = \App\Visualization::where('name', $vis->name)->first();
        $visUser = $visualization->users()->where('user_id', $user->id)->first();
        if ($visUser !== null) {
            $userRating = $visUser->pivot->rating;
            if ($userRating !== null) {
                if ($userRating == 1) {
                    return 0.75;
                } else if ($userRating == 0) {
                    return 0.25;
                } else {
                    return $userRating;
                }
            } else {
                return $this->generateUserRating($visualization->id, 2, 3);
            }
        } else {
            return $this->generateUserRating($visualization->id, 2, 3);
        }

    }

    private function userDeviceInformation($visualization, $isMobile) {
        $rating = 0;
        // user rating
        $user = Auth::user();
        $userknowledge = $user->visualizations()->where('name', $visualization->name)->first();
        $rating += ($userknowledge->pivot->knowledge / 5) * 100;

        if ($isMobile) {
            // device rating
            $device = $visualization->systems()->where('name', 'tablet')->first();
        } else {
            $device = $visualization->systems()->where('name', 'desktop')->first();
        }

        $rating += ($device->pivot->rating / 3) * 100;
        return $rating/2;
    }

    // sorter function
    public static function cmp($a, $b) {
       return $b->rating - $a->rating;
    }

    public function generateVectors() {
        $visVector = [];
        foreach (Visualization::all() as $visualization) {
            $vector = [];
            foreach (User::all() as $user) {
                $rating = $visualization->users()->where('user_id', $user->id)->first();
                if ($rating == null) {
                    $vector[] = 0.5;
                } else {
                    if ($rating->pivot->rating === null) {
                        $vector[] = 0.5;
                    } else {
                        $vector[] = $rating->pivot->rating;
                    }
                }
            }
            $visVector[] = $vector;
        }
        return $visVector;
    }

    // Using cosine based similarity
    public function vectorSimilarity($vectorA, $vectorB) {
        $vectors = [$vectorA, $vectorB];
        if (count($vectorA) == count($vectorB)) {
            // calculate dot product
            $dotproduct = 0;
            for ($i=0; $i < count($vectorA); $i++) { 
                $dotproduct += $vectorA[$i] * $vectorB[$i];
            }

            // calculate scalar
            $scalars = [];
            foreach ($vectors as $vector) {
                $scalar = 0;
                foreach ($vector as $value) {
                    $scalar += $value * $value;
                }
                $scalars[] = $scalar;
            }

            return $dotproduct/$scalars[1];
        } else {
            // error handling
        }
    }

    // input : $similaritems = array of {similarity: x, visualization: id}
    public function generatePrediction($similaritems, $vectors) {
        $weight = 0;
        $sum = 0;
        $user = Auth::user();
        $users = \App\User::all();
        $index = 0;
        for ($i=0; $i < count($users); $i++) { 
            if ($users[$i]->id == $user->id) {
                $index = $i;
                break;
            }
        }

        foreach ($similaritems as $similaritem) {
            $visualization = Visualization::find($similaritem->visualization);
            $rating = $vectors[$similaritem->visualization - 1][$index];

            $weight += $similaritem->similarity * $rating;
            $sum += $similaritem->similarity;
        }
        return $weight / $sum;
    }

    // neighbors : calculate based on how many closest neighbors, minVisRating : treshold of how many rating a visualization need to have
    public function generateUserRating($visualizationid, $neighbors, $minVisRating) {
        $visualizationVectors = $this->generateVectors();
        $vectors = [];

        $vector = $visualizationVectors[$visualizationid - 1];
        for ($i=1; $i <= count($visualizationVectors); $i++) {
            if (count(Visualization::all()[$i - 1]->users) >= $minVisRating) {
                $similaritem = (object) [];
                if ($i == $visualizationid) {
                
                } else {
                    $similaritem->similarity = $this->vectorSimilarity($vector, $visualizationVectors[$i]);
                    $similaritem->visualization = $i;
                    $vectors[] = $similaritem;
                }
            } else {
                // skip if the visualization does not have enough rating by the users
            }
        }
        // sort mapping
        usort($vectors, array("App\Http\Controllers\RatingController", "compareSimilarity"));

        $similaritems = [];
        for ($i=0; $i < $neighbors && $i < count($vectors); $i++) { 
            $similaritems[] = $vectors[$i];
        }

        if (count($similaritems) === 0) {
            return null;
        } else {
            return $this->generatePrediction($similaritems, $visualizationVectors);
        }
    }

    // sorter function
    public static function compareSimilarity($a, $b) {
       return $b->similarity - $a->similarity;
    }
}
