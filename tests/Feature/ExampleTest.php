<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use SergiX44\Nutgram\Nutgram;

class ExampleTest extends TestCase {
    /**
    * A basic test example.
    */

    public function test_the_application_returns_a_successful_response(): void {
        $response = $this->get( '/' );

        $response->assertStatus( 200 );

    }

    /**
    * @return void
    */

    public function test_bot() {
        $bot = app( Nutgram::class );
    }
}
