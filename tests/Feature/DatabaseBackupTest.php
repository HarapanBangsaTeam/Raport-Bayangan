<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_download_database_backup(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.backup.download'));

        $response->assertOk();
        $this->assertStringContainsString('attachment;', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('Backup_Raport_SMPHB_', $response->headers->get('content-disposition', ''));
        $this->assertStringContainsString('.sqlite', $response->headers->get('content-disposition', ''));
    }

    public function test_user_cannot_restore_invalid_extension(): void
    {
        $invalidFile = UploadedFile::fake()->create('malicious.php', 10, 'text/plain');

        $response = $this->actingAs($this->user)->post(route('settings.backup.restore'), [
            'backup_file' => $invalidFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_user_cannot_restore_corrupted_sqlite_header(): void
    {
        // Berkas berekstensi .sqlite tapi bukan format binary SQLite yang valid
        $fakeFile = UploadedFile::fake()->createWithContent('corrupted.sqlite', 'Bukan format SQLite asli');

        $response = $this->actingAs($this->user)->post(route('settings.backup.restore'), [
            'backup_file' => $fakeFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Berkas yang diunggah bukan berkas database SQLite yang valid.');
    }

    public function test_user_can_restore_valid_sqlite_backup(): void
    {
        $dbPath = database_path('database.sqlite');

        if (! file_exists($dbPath)) {
            $this->markTestSkipped('Database SQLite file not present in test environment.');
        }

        $validContent = file_get_contents($dbPath);
        $validFile = UploadedFile::fake()->createWithContent('valid_backup.sqlite', $validContent);

        $response = $this->actingAs($this->user)->post(route('settings.backup.restore'), [
            'backup_file' => $validFile,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
