<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use DiDom\Document;

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
    $filteredDataFromUrl = array_slice(parse_url($request->name), 0, 2);
    $normalizedUrl = implode("://", $filteredDataFromUrl);
    $domain = DB::table('domains')->where('name', $request->name)->first();
    if ($domain) {
        flash('Url already exists');
        return redirect()->route('domains.show', $domain->id);
    } else {
        $id = DB::table('domains')->insertGetId([
            'name' => $normalizedUrl,
            'created_at' => Carbon::now(),
            'created_at' => Carbon::now()
        ]);
        flash('Url has been added');
        return redirect()->route('domains.show', $id);
    }
})->name('domains.store');

Route::get('/domains/{id}', function ($id) {
    $domain = DB::table('domains')->find($id);
    $domainChecks = DB::table('domain_checks')->where('domain_id', $id)->get();
    return view('domain.show', compact('domain', 'domainChecks'));
})->name('domains.show');

Route::post('/domains/{id}/checks', function ($id) {
    try {
        $response = Http::get(DB::table('domains')->find($id)->name);
    } catch (\Exception $e) {
        flash($e->getMessage())->error();
        return redirect()->route('domains.show', $id);
    }
    $document = new Document($response->body());
    $domainChecks = [
        'domain_id' => $id,
        'keywords' => $document->first('meta[name=keywords]')->content ?? null,
        'description' => $document->first('meta[name=description]')->content ?? null,
        'h1' => $document->first('h1') ? $document->first('h1')->text() : null,
        'status_code' => $response->status(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now()
    ];
    $domain = DB::table('domain_checks')->insert($domainChecks);
    flash("Website has been checked!")->success();
    return redirect()->route('domains.show', $id);
})->name('domains.checks.store');
