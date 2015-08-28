<?php

namespace App\Http\Controllers;

use Input;
use Validator;
use Redirect;
use Request;
use Session;
use Schema;
use DB;
use View;

class DataController extends Controller 
{
  public function getIndex()
  {
    $hasVisualization = [];
    foreach (\App\Dataset::all() as $dataset) {
      //var_dump($dataset->projects);exit();
      if (count($dataset->projects) > 0) {
        $hasVisualization[] = true;
      } else {
        $hasVisualization[] = false;
      }
    }
    return view('dataset.selectdataset', [
        'data' => \App\Dataset::all()->toArray(),
        'columnnames' => Schema::getColumnListing('datasets'),
        'hasVisualization' => $hasVisualization
    ]);  
  }

  public function getUpload()
  {
    return View::make('dataset.upload'); 
  }

  public function postUpload() 
  {
    $file = Input::file('dataset');
    // checking file is valid.
    if (Input::file('dataset')->isValid()) {
      $destinationPath = 'uploads'; // upload path
      $extension = Input::file('dataset')->getClientOriginalExtension(); // getting dataset extension
      $fileName = $file->getClientOriginalName(); // renaming dataset
      Input::file('dataset')->move($destinationPath, $fileName); // uploading file to given path

      $path = $destinationPath.'/'.$fileName;
      $csv = array_map("str_getcsv", file($path, FILE_SKIP_EMPTY_LINES));
      $tablename = basename($destinationPath.'/'.$fileName, '.csv');

      Session::put('csv', $csv);
      Session::put('csvpath', $path);

      // show uploaded data
      return view('dataset.uploadsetting', [
            'tablename' => $tablename,
            'attributes' => $csv[0]
      ]);
    }
    else {
      // sending back with error message.
      Session::flash('error', 'uploaded file is not valid');
      return Redirect::to('dataset/upload');
    }
  }

  public function getEdit($id)
  {
    $dataset = \App\Dataset::find($id);
    return view('dataset.editdataset', [
      'attributes' => $dataset->attributes,
      'datasetid' => $id
    ]);
  }

  public function postEdit()
  {
    $name = Input::get('name');
    $type = Input::get('type');
    $dataset = \App\Dataset::find(Input::get('datasetid'));
    foreach ($dataset->attributes as $attribute) {
      $attribute->name = $name[$attribute->id];
      $attribute->data_variable_type = $type[$attribute->id];
      $attribute->save();
    }

    return redirect('dataset');
  }

  public function getDelete($id)
  {
    $dataset = \App\Dataset::find($id);
    Schema::dropIfExists($dataset->table_name);
    foreach ($dataset->attributes as $attribute) {
      $attribute->delete();
    }
    $dataset->delete();

    return redirect('dataset');
  }

  public function postConfiguration() 
  { 
    $tablename = Input::get('tablename');
    $csv = Session::get('csv');
    $path = Session::get('csvpath');

    // check if there is a table with the same name in the database
    if(!Schema::connection('dataset')->hasTable($tablename)){
      // Save to database  
      $this->saveToDB($path, $tablename, $csv[0], $csv);
      // sending back with message
      Session::flash('success', 'Uploaded successfully'); 
    } else {
      // sending back with message
      Session::flash('error', 'Dataset with the same name already exist in database');
    }

    return Redirect::to('dataset/upload');
  }

  public function checkData()
  {
    $file = Input::file('dataset');
    // checking file is valid.
    if (Input::file('dataset')->isValid()) {
      $destinationPath = 'uploads'; // upload path
      $extension = Input::file('dataset')->getClientOriginalExtension(); // getting dataset extension
      $fileName = $file->getClientOriginalName(); // renaming dataset
      Input::file('dataset')->move($destinationPath, $fileName); // uploading file to given path

      $path = $destinationPath.'/'.$fileName;
      $csv = array_map("str_getcsv", file($path, FILE_SKIP_EMPTY_LINES));
      $tablename = basename($destinationPath.'/'.$fileName, '.csv');
      
      var_dump(DB::connection('dataset')->table($tablename)->select('BUS/Truck')->distinct()->get()); exit();
    }
  }

  private function saveToDB($path, $tablename, $header, $data) {
    // create new table on database 'kakas_database'
    DB::connection('dataset')->statement($this->constructScript($header, $tablename, $data));
    $keys = array_shift($data);
    foreach ($data as $i=>$row) {
        // insert the data
        DB::connection('dataset')->table($tablename)->insert(array_combine($keys, $row));
    }

    // save metamodel to main db
    $this->saveMetamodel($tablename, $header, $data);
  }

  private function saveMetamodel($tablename, $header, $data) {
    // save to datasets table
    $dataset = new \App\Dataset;
    $dataset->table_name = $tablename;
    $dataset->created_at = date("Y-m-d H:i:s");
    $dataset->updated_at = date("Y-m-d H:i:s");
    $dataset->save();

    // save to attributes table
    $this->saveAttributes($dataset, $tablename, $header, $data);
  }

  /*
  *   TODO : Check if a column is numeric from all instances, not just the first
  */
  private function saveAttributes($dataset, $tablename, $header, $data) {
    for ($i=0; $i < count($header); $i++) { 
      // check if data is numeric
      //var_dump($data[0]);exit();
      $isnumeric = is_numeric($data[0][$i]);
      $quantity = DB::connection('dataset')->table($tablename)->select($header[$i])->count();
      $cardinality = count(DB::connection('dataset')->table($tablename)->select($header[$i])->distinct()->get());
      
      $attribute = new \App\Attribute;
      $attribute->name = $header[$i];
      $attribute->quantity = $quantity;
      $attribute->cardinality = $cardinality;
      $attribute->data_variable_type = $this->getDataType($cardinality, $quantity, $isnumeric);

      $dataset->attributes()->save($attribute);
      //$attribute->save();
    }
  }

  private function getDataType($cardinality, $quantity, $isnumeric) {
    if ($isnumeric) {
      return "kuantitatif";
    } else if ($cardinality == $quantity) {
      return "nominal";
    } else {
      return "ordinal";
    }
  }

  private function constructScript($header, $tablename, $data) {
    $columndata = "";
    for ($i=0; $i < count($header) - 1; $i++) {
      if (is_numeric($data[1][$i])){
        $columndata = $columndata."`".$header[$i]."` INT(100) NULL,\n";
      } else {
        $columndata = $columndata."`".$header[$i]."` VARCHAR(100) NULL,\n";
      }
    }
    // special case for last column
    if (is_numeric($data[1][count($header) -1])){
      $columndata = $columndata."`".$header[count($header) - 1]."` INT(100) NULL";
    } else {
      $columndata = $columndata."`".$header[count($header) - 1]."` VARCHAR(100) NULL";
    }


    return "CREATE TABLE IF NOT EXISTS `".$tablename."` (
        ".$columndata."
    )ENGINE = InnoDB;";
  }

  public function getSelection($id) {
    if ($id == null) {
      return redirect('setup');
    } else {
      // load project
      $project = \App\VisualizationProject::findOrFail($id);

      // load selected dataset
      $dataset = $project->dataset;
      $dataSelections = [];

      $headers = Schema::connection('dataset')->getColumnListing($dataset->table_name);

      $stringHeaders = [];

      foreach ($headers as $header) {
        $cell = DB::connection('dataset')->table($dataset->table_name)->select($header)->first()->$header;
        if (!is_numeric($cell)) {
          $stringHeaders[] = $header;
        }
      }
      
      $data = DB::connection('dataset')->table($dataset->table_name);

      $sortdata = null;

      foreach ($project->dataSelections as $selection) {
        if ($selection->operand == "##SORTBY##") {
          $sortdata = $selection;
        } else {
          $data = $data->where($selection->column_name, $selection->operator, $selection->operand);
        }
        $dataSelections[] = $selection;
      }

      if ($sortdata !== null) {
          $sorttype = ($sortdata->operator == '>') ? 'asc' : 'desc';
          $data = $data->orderBy($sortdata->column_name, $sorttype);
      }

      $data = $data->get();
      
      return view('dataset.dataselection', [
          'projectid' => $id,
          'data' => $data,
          'filters' => $dataSelections,
          'columnnames' => $headers,
          'stringColumns' => $stringHeaders
      ]);  
    }
  }

  public function postSelection() {
    $columnName = Input::get('column');
    $operator = Input::get('operator');
    $operand = Input::get('operand');
    $projectid = Input::get('projectid');
    $project = \App\VisualizationProject::findOrFail($projectid);

    $selection = new \App\DataSelection;
    $selection->column_name = $columnName;
    if ($operator == 'start') {
      $selection->operator = 'LIKE';
      $selection->operand = $operand.'%';
    } elseif ($operator == 'with') {
      $selection->operator = 'LIKE';
      $selection->operand = '%'.$operand.'%';
    } elseif ($operator == 'end') {
      $selection->operator = 'LIKE';
      $selection->operand = '%'.$operand;
    } else {
      $selection->operator = $operator;
      $selection->operand = $operand;
    }

    $project->dataSelections()->save($selection);

    return Redirect::to('/dataset/selection/'.$projectid);
  }

  public function postRemove() {
    $projectid = Input::get('projectid');
    $project = \App\VisualizationProject::findOrFail($projectid);
    $selectionid = Input::get('selectionid');

    $project->dataSelections()->where('id', $selectionid)->first()->delete();
    return Redirect::to('/dataset/selection/'.$projectid);
  }
}