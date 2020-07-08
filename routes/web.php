<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
use DiDom\Query;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

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
    return view('welcome');
})->name('index');

Route::get('/domains', function () {
    $domains = DB::table('domains')
    ->select('domains.id', 'domains.name', 'domains.created_at', 'domain_checks.status_code', DB::raw('MAX(domain_checks.updated_at) as last_check'))
    ->leftJoin('domain_checks', 'domains.id', '=', 'domain_checks.domain_id')
    ->groupBy('domains.id', 'domain_checks.status_code')->get();

    return view('domain.index', compact('domains'));
})->name('domains.index');

Route::post('/domains', function (Request $request) {
    $validator = Validator::make($request->all(), ['name' => 'required|url']);
    if ($validator->fails()) {
        flash('Not a valid url')->error();
        return redirect()->route('index');
    }
    $sheme = parse_url($request->name, PHP_URL_SCHEME);
    $host = parse_url($request->name, PHP_URL_HOST);
    $domain = DB::table('domains')->where('name', $request->name)->first();
    if ($domain) {
        flash('Url already exists');
        return redirect()->route('domains.show', $domain->id);
    } else {
        $id = DB::table('domains')->insertGetId([
            'name' => join("://", [$sheme, $host]),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        flash('Url has been added');
        return redirect()->route('domains.show', $id);
    }
})->name('domains.store');

Route::get('/domains/{id}', function ($id) {
    $domain = DB::table('domains')->find($id);
    abort_unless($domain, 404);
    $domainChecks = DB::table('domain_checks')->where('domain_id', $id)->get();
    foreach ($domainChecks as $check) {
        $check->h1 = Str::limit($check->h1, 25);
        $check->description = Str::limit($check->description, 25);
        $check->keywords = Str::limit($check->keywords, 25);
    }
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
        'keywords' => optional($document->first('meta[name=keywords]'))->content,
        'description' => optional($document->first('meta[name=description]'))->content,
        'h1' => optional($document->first('h1'))->text(),
        'status_code' => $response->status(),
        'created_at' => now(),
        'updated_at' => now()
    ];
    $domain = DB::table('domain_checks')->insert($domainChecks);
    flash("Website has been checked!")->success();
    return redirect()->route('domains.show', $id);
})->name('domains.checks.store');
