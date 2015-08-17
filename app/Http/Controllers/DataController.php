<?php

namespace App\Http\Controllers;

use Input;
use Validator;
use Redirect;
use Request;
use Session;
use Schema;
use DB;

class DataController extends Controller 
{
  public function upload() 
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
      
      // Save to database  
      $this->saveToDB($path, $tablename, $csv[0], $csv);

      // sending back with message
      Session::flash('success', 'Uploaded successfully'); 
      return Redirect::to('upload');
      //return response()->json($this->saveToDB($path, $tablename, $csv[0], $csv));
    }
    else {
      // sending back with error message.
      Session::flash('error', 'uploaded file is not valid');
      return Redirect::to('upload');
    }
  }

  private function saveToDB($path, $tablename, $header, $data) {
    // create new table on database 'kakas_database'
    DB::connection('dataset')->statement($this->constructScript($header, $tablename));

    $keys = array_shift($data);
    foreach ($data as $i=>$row) {
        $data[$i] = array_combine($keys, $row);
    }

    // insert the data
    DB::connection('dataset')->table($tablename)->insert($data);

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
      $isnumeric = is_numeric($data[0][$header[$i]]);
      $quantity = DB::connection('dataset')->table($tablename)->select($header[$i])->count();
      $cardinality = DB::connection('dataset')->table($tablename)->select($header[$i])->distinct()->count();
      
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

  private function constructScript($header, $tablename) {
    $columndata = "";
    for ($i=0; $i < count($header) - 1; $i++) { 
      $columndata = $columndata."`".$header[$i]."` VARCHAR(20) NULL,\n";
    }
    // special case for last column
    $columndata = $columndata."`".$header[count($header) - 1]."` VARCHAR(20) NULL\n";


    return "CREATE TABLE IF NOT EXISTS `".$tablename."` (
        ".$columndata."
      )
      ENGINE = InnoDB;";
  }
}