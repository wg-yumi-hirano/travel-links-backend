<?php declare(strict_types=1);

namespace Tests\Feature;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BaseTestCase extends TestCase
{
    protected function getApi($uri)
    {
        return $this->withSession(['store' => []])->getJson($uri, $this->withFrontendHeaders());
    }

    protected function getAuthApi($uri, $user)
    {
        Sanctum::actingAs($user, ['*']);
        return $this->getJson($uri, $this->withFrontendHeaders());
    }

    protected function postAuthApi($uri, $parameters, $user)
    {
        Sanctum::actingAs($user, ['*']);
        return $this->postJson($uri, $parameters, $this->withFrontendHeaders());
    }

    protected function putAuthApi($uri, $parameters, $user)
    {
        Sanctum::actingAs($user, ['*']);
        return $this->putJson($uri, $parameters, $this->withFrontendHeaders());
    }

    protected function deleteAuthApi($uri, $parameters, $user)
    {
        Sanctum::actingAs($user, ['*']);
        return $this->deleteJson($uri, $parameters, $this->withFrontendHeaders());
    }

    private function withFrontendHeaders(array $headers = []): array
    {
        return array_merge([
            'Referer' => config('app.url'),
            'Origin' => config('app.url'),
            'Accept' => 'application/json',
        ], $headers);
    }
}
