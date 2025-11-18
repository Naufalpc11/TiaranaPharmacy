<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_chatbot_page_can_be_loaded(): void
    {
        $this->withoutVite();

        $response = $this->get('/chatbot');

        $response->assertStatus(200);
    }
}
