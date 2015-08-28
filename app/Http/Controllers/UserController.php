<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use Session;
use Auth;

class UserController extends Controller
{
    // TODO: HARDCODED! CHANGE IF POSSIBLE
     public function getIndex()
    {   
        $visualizations = [];
        for ($i = 0; $i < 8; $i++) {
            $visualizations[] = \App\Visualization::all()[$i];
        }

        return view('user.userpreference', [
            'visualizations' => $visualizations
        ]);
    }

    public function postPreference() {
        $preference = Input::get('preference');
        $user = Auth::user();

        foreach ($preference as $id => $score) {
            $userpreference = $user->visualizations()->where('id', $id)->first();
            if ($userpreference === null) {
                $visualization = \App\Visualization::find($id);
                $user->visualizations()->attach($visualization, ['knowledge' => $score]);
            } else {
                $userpreference->pivot->knowledge = $score;
                $userpreference->pivot->save();
                $userpreference->save();
            }
        }
        return redirect('home');
    }

}
