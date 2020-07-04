<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

Route::get('/', function () {
    return view('domain.create');
})->name('domains.create');

Route::get('/domains', function () {
    $domains = DB::table('domains')->paginate(10);
    return view('domain.index', ['domains' => $domains]);
})->name('domains.index');

Route::post('/domains', function (Request $request) {
    $data = $request->validate(['name' => ['required', 'unique:domains', 'url']]);
    $name = parse_url($data['name'], PHP_URL_SCHEME) . "://" . parse_url($data['name'], PHP_URL_HOST);
    $id = DB::table('domains')->insertGetId(['name' => $name, 'created_at' => Carbon::now()->toDateTimeString()]);
    flash('Message');
    return redirect()->route('domains.show', $id);
})->name('domains.store');

Route::get('/domains/{id}', function ($id) {
    $domain = DB::table('domains')->find($id);
    return view('domain.show', ['domain' => $domain]);
})->name('domains.show');
