<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_access_import_export_page()
    {
        $response = $this->get('/admin/import-export');

        $response->assertStatus(200);
        $response->assertSee('Import & Export Data');
    }

    /** @test */
    public function it_validates_upload_import_requires_file()
    {
        $response = $this->post('/admin/import-data', [
            'import_source' => 'upload',
            // Missing 'file' field
        ]);

        $response->assertSessionHasErrors('file');
    }

    /** @test */
    public function it_validates_storage_import_requires_storage_file()
    {
        $response = $this->post('/admin/import-data', [
            'import_source' => 'storage',
            // Missing 'storage_file' field
        ]);

        $response->assertSessionHasErrors('storage_file');
    }

    /** @test */
    public function it_accepts_valid_upload_import_request()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->create('test.json', 100, 'application/json');

        $response = $this->post('/admin/import-data', [
            'import_source' => 'upload',
            'file' => $file,
        ]);

        // Should not have validation errors for the file field
        $response->assertSessionDoesntHaveErrors('file');
    }

    /** @test */
    public function it_accepts_valid_storage_import_request()
    {
        $response = $this->post('/admin/import-data', [
            'import_source' => 'storage',
            'storage_file' => 'backup-2025-11-04_19-26-13.json',
        ]);

        // Should not have validation errors for the storage_file field
        $response->assertSessionDoesntHaveErrors('storage_file');
    }
}