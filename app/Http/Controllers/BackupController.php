<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    /**
     * Download salinan berkas database SQLite saat ini.
     */
    public function download(): BinaryFileResponse|RedirectResponse
    {
        $dbPath = config('database.connections.sqlite.database');

        if ($dbPath === ':memory:' || ! is_string($dbPath) || ! file_exists($dbPath)) {
            $dbPath = database_path('database.sqlite');
        }

        if (! is_string($dbPath) || ! file_exists($dbPath)) {
            return back()->with('error', 'Berkas database SQLite tidak ditemukan pada server.');
        }

        $timestamp = date('Y-m-d_His');
        $filename = "Backup_Raport_SMPHB_{$timestamp}.sqlite";

        return response()->download($dbPath, $filename, [
            'Content-Type' => 'application/x-sqlite3',
        ]);
    }

    /**
     * Pulihkan database SQLite dari berkas cadangan yang diunggah.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:51200'],
        ]);

        $uploadedFile = $request->file('backup_file');
        $originalExt = strtolower($uploadedFile->getClientOriginalExtension());

        if (! in_array($originalExt, ['sqlite', 'sqlite3', 'db', 'bak'])) {
            return back()->with('error', 'Format berkas tidak valid. Harap unggah berkas berekstensi .sqlite, .sqlite3, atau .db.');
        }

        // Verifikasi header SQLite (16 byte pertama harus 'SQLite format 3\000')
        $handle = fopen($uploadedFile->getRealPath(), 'rb');
        $header = fread($handle, 16);
        fclose($handle);

        if ($header !== "SQLite format 3\000") {
            return back()->with('error', 'Berkas yang diunggah bukan berkas database SQLite yang valid.');
        }

        $dbPath = config('database.connections.sqlite.database');
        $isMemory = ($dbPath === ':memory:');
        if ($isMemory || ! is_string($dbPath)) {
            $dbPath = database_path('database.sqlite');
        }

        // Buat salinan cadangan darurat sebelum ditimpa
        if (file_exists($dbPath)) {
            copy($dbPath, $dbPath.'.bak');
        }

        if (! $isMemory) {
            DB::disconnect();
        }

        try {
            copy($uploadedFile->getRealPath(), $dbPath);

            if (! $isMemory) {
                DB::reconnect();
                DB::table('users')->count();
            }

            return redirect()->route('settings.school')->with(
                'success',
                'Database berhasil dipulihkan dari berkas cadangan. Seluruh data nilai dan raport telah diperbarui.'
            );
        } catch (\Throwable $e) {
            // Pulihkan dari cadangan darurat jika terjadi kegagalan
            if (file_exists($dbPath.'.bak')) {
                copy($dbPath.'.bak', $dbPath);
                if (! $isMemory) {
                    DB::reconnect();
                }
            }

            return back()->with('error', 'Gagal memulihkan database: '.$e->getMessage().'. Data dikembalikan ke kondisi sebelumnya.');
        }
    }
}
