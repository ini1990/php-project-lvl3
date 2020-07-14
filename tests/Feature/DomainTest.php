<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DomainTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    protected function setUp(): void
    {
        parent::setUp();

        $domain = $this->faker->url;
        $sheme = parse_url($domain, PHP_URL_SCHEME);
        $host = parse_url($domain, PHP_URL_HOST);
        $this->host = $host;
        $this->id = \DB::table('domains')->insertGetId([
            'name' => join("://", [$sheme, $host]),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $this->statusCode = 200;
        $domainChecks = [
            'domain_id' => $this->id,
            'status_code' => $this->statusCode,
            'created_at' => now(),
            'updated_at' => now()
        ];
        \DB::table('domain_checks')->insert($domainChecks);
    }

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

    public function testShow()
    {
        $response = $this->get(route('domains.show', $this->id));
        $response->assertOk();
        $response->assertSee($this->host);
        $response->assertSee($this->statusCode);
    }

    public function testIndex()
    {
        $response = $this->get(route('domains.index'));
        $response->assertOk();
        $response->assertSee($this->host);
        $response->assertSee($this->statusCode);
    }
}
