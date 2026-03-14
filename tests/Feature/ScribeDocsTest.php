<?php

declare(strict_types=1);

it('generates and serves the scribe documentation', function (): void {
    $this->artisan('scribe:generate')
        ->assertExitCode(0);

    expect(base_path('storage/app/scribe/openapi.yaml'))->toBeFile();
    expect(base_path('storage/app/scribe/collection.json'))->toBeFile();

    $this->get('/docs')->assertOk();
    $this->get('/docs.openapi')->assertOk();
    $this->get('/docs.postman')->assertOk();
});
