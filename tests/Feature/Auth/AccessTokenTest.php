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

        $data = $this->validCredentials(['email' => $user->email]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $token = $response->json('plain-text-token');
        $dbToken = PersonalAccessToken::findToken($token);

        $this->assertTrue($dbToken->tokenable->is($user));
    }

    /** @test */
    public function password_must_be_valid()
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data = $this->validCredentials([
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function user_must_be_registered()
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials();

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors('email');
    }

    /** @test */
    public function email_is_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['email' => null]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['email' => 'required']);
    }

    /** @test */
    public function email_must_be_valid()
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['email' => 'invalid-email']);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['email' => 'email']);
    }

    /** @test */
    public function password_is_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['password' => null]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['password' => 'required']);
    }

    /** @test */
    public function device_name_is_required()
    {
        $this->withoutJsonApiDocumentFormatting();

        $data = $this->validCredentials(['device_name' => null]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $response->assertJsonValidationErrors(['device_name' => 'required']);
    }

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'email' => 'nmabregu@gmail.com',
            'password' => 'password',
            'device_name' => 'test-device',
        ], $overrides);
    }
}
