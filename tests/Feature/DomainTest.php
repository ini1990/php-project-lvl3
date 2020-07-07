<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Carbon\Carbon;

class DomainTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;


    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testStore()
    {
        $domain = $this->faker->url;
        $response = $this->post(route('domains.store', ['name' => $domain]));
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $response->assertRedirect();
        $sheme = parse_url($domain, PHP_URL_SCHEME);
        $host = parse_url($domain, PHP_URL_HOST);
        $this->assertDatabaseHas('domains', ['name' => join("://", [$sheme, $host])]);
    }

    public function testChecksStore()
    {
        $html = file_get_contents(__DIR__ . "/../fixtures/test.html");
        $url = "http://example.test";
        $id = \DB::table('domains')->insertGetId([
            'name' => $url,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        Http::fake([
            $url => Http::response($html)
        ]);
        $response = $this->post(route('domains.checks.store', $id));
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();
        $this->assertDatabaseHas('domain_checks', ["keywords" => "test keywords",
        'h1' => 'test h1', 'description' => 'test description']);
    }


    public function testShow()
    {
        $domain = $this->faker->url;
        $sheme = parse_url($domain, PHP_URL_SCHEME);
        $host = parse_url($domain, PHP_URL_HOST);
        $id = \DB::table('domains')->insertGetId([
            'name' => join("://", [$sheme, $host]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $response = $this->get(route('domains.show', $id));
        $response->assertOk();
    }

    public function testIndex()
    {
        $response = $this->get(route('domains.index'));
        $response->assertOk();
    }
}
