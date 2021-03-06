<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use DiDom\Document;
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
    $domains = DB::table('domains')->orderBy("id")->get();
    $lastChecks = DB::table('domain_checks')
        ->distinct("domain_id")
        ->orderBy("domain_id")
        ->orderBy("created_at", "desc")
        ->whereIn('domain_id', $domains->pluck('id'))
        ->get()->keyBy('domain_id');
    return view('domain.index', compact('domains', 'lastChecks'));
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
    return view('domain.show', compact('domain', 'domainChecks'));
})->name('domains.show');

Route::post('/domains/{id}/checks', function ($id) {
    $domain = DB::table('domains')->find($id);
    try {
        $response = Http::get($domain->name);
    } catch (\Exception $e) {
        flash($e->getMessage())->error();
        return redirect()->route('domains.show', $id);
    }
    $body = $response->body();
    $status = $response->status();
    $document = new Document($body);
    preg_match('/<h1[^>]*>\s*(.*?)\s*<\/h1>/i', $document, $result);
    $domainChecks = [
        'domain_id' => $id,
        'keywords' => optional($document->first('meta[name=keywords]'))->content,
        'description' => optional($document->first('meta[name=description]'))->content,
        'h1' => optional($result)[1],
        'status_code' => $status,
        'created_at' => now(),
        'updated_at' => now()
    ];

    DB::table('domain_checks')->insert($domainChecks);
    DB::table('domains')->where('id', $id)->update(['updated_at' => $domainChecks['created_at']]);
    flash("Website has been checked!")->success();
    
    return redirect()->route('domains.show', $id);
})->name('domains.checks.store');
