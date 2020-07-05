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
    $latestCkecks = DB::table('domain_checks')
    ->select('domain_id', DB::raw('MAX(created_at) as last_domain_check_at'))
    ->groupBy('domain_id');
    $domains = DB::table('domains')
    ->leftJoinSub($latestCkecks, 'latest_checks', function ($join) {
        $join->on('domains.id', '=', 'latest_checks.domain_id');
    })->get();
    return view('domain.index', ['domains' => $domains]);
})->name('domains.index');

Route::post('/domains', function (Request $request) {
    $validator = Validator::make($request->all(), ['name' => 'required|url']);
    if ($validator->fails()) {
        flash('Not a valid url')->error();
        return redirect()->route('domains.create');
    }
    $name = parse_url($request->name, PHP_URL_SCHEME) . "://" . parse_url($request->name, PHP_URL_HOST);
    $domain = DB::table('domains')->where('name', $name)->first();
    if ($domain) {
        flash('Url already exists');
        return redirect()->route('domains.show', $domain->id);
    } else {
        $id = DB::table('domains')->insertGetId(['name' => $name, 'created_at' => Carbon::now()->toDateTimeString()]);
        flash('Url has been added')->success();
        return redirect()->route('domains.show', $id);
    }
})->name('domains.store');

Route::get('/domains/{id}', function ($id) {
    $domain = DB::table('domains')->find($id);
    $domainChecks = DB::table('domain_checks')->where('domain_id', $id)->get();
    return view('domain.show', compact('domain', 'domainChecks'));
})->name('domains.show');

Route::post('/domains/{id}/checks', function ($id) {
    $domainChecks = [
        'domain_id' => $id,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ];
    $domain = DB::table('domain_checks')->insert($domainChecks);
    return redirect()->route('domains.show', $id);
})->name('domains.checks.store');
