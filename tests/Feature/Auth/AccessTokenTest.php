<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_issue_access_tokens()
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        $token = $response->json('plain-text-token');
        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->tokenable->is($user));
    }
}
