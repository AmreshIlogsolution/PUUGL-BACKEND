<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// $user = DB::table('User_Rights')->select('uID', 'uDept', 'contact_no', 'emailid')->get();
// $totalUser = count($user);
// $combinedArray = array();
// $collection = collect(['name', 'age', 'mobile', 'email']);
// for ($i = 0; $i < $totalUser; $i++) {
//     $combined = $collection->combine([$user[$i]->uID, $user[$i]->uDept, $user[$i]->contact_no, $user[$i]->emailid]);

//     $combined->all();
//     array_push($combinedArray, $combined->all());
// }

// dd($combinedArray);




// Collection::macro('toUpper', function () {
//     return $this->map(function (string $value) {
//         return Str::upper($value);
//     });
// });

// $collection = collect(['first', 'second', 'third', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten']);

// $upper = $user->collection();
// dd($upper);

Route::get('/', function () {
    return view('welcome');
});
