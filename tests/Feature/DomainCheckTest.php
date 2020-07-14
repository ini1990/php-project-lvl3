<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DomainCkeckTest extends TestCase
{
    use RefreshDatabase;

    public function testChecksStore()
    {
        $html = file_get_contents(__DIR__ . "/../fixtures/test.html");
        $url = "http://example.test";
        $id = \DB::table('domains')->insertGetId([
            'name' => $url,
            'created_at' => now(),
            'updated_at' => now()
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
}