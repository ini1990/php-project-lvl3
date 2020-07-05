<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function testCreate()
    {
        $response = $this->get(route('domains.create'));
        $response->assertStatus(200);
    }

    public function testStore()
    {
        $domain = $this->faker->url;
        $response = $this->post(route('domains.store', ['name' => $domain]));
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $response->assertRedirect();
        $this->assertDatabaseHas('domains', ['name' => parse_url($domain, PHP_URL_SCHEME) . "://" . parse_url($domain, PHP_URL_HOST)]);
    }

    public function testChecksStore()
    {
        $domain = $this->faker->url;
        $url = parse_url($domain, PHP_URL_SCHEME) . "://" . parse_url($domain, PHP_URL_HOST);
        $id = \DB::table('domains')->insertGetId([
            'name' => $url,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $response = $this->post(route('domains.checks.store', $id));
        $response->assertSessionHasNoErrors();
        $response->assertStatus(302);
        $response->assertRedirect();
        $this->assertDatabaseHas('domain_checks', ['domain_id' => $id]);
    }


    public function testShow()
    {
        $domain = $this->faker->url;
        $url = parse_url($domain, PHP_URL_SCHEME) . "://" . parse_url($domain, PHP_URL_HOST);
        $id = \DB::table('domains')->insertGetId([
            'name' => $url,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $response = $this->get(route('domains.show', [$id]));
        $response->assertOk();
    }

    public function testIndex()
    {
        $response = $this->get(route('domains.index'));
        $response->assertOk();
    }
}
