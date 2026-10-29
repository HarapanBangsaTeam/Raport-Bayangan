<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\SchoolSetting;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicYearManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Classroom $classroom;

    protected Subject $subject;

    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->classroom = Classroom::create(['name' => 'VII-A']);
        $this->subject = Subject::create([
            'name' => 'Matematika',
            'code' => 'MTK',
            'order_number' => 1,
            'is_active' => true,
        ]);

        SchoolSetting::create([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8',
            'nama_kepala_sekolah' => 'Juliana Ruth Mathilda Hutapea, S.Th',
            'nip_kepala_sekolah' => '02980708020780',
            'nama_wali_kelas' => 'Farida Donna Gultom, S.Pd',
            'nip_wali_kelas' => '-',
            'judul_laporan' => 'Laporan Hasil Belajar Tengah Semester 1',
            'tanggal_raport' => 'Karawang, 02 Oktober 2026',
        ]);
    }

    public function test_user_can_view_academic_years_index(): void
    {
        $year1 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('academic-years.index'));

        $response->assertOk();
        $response->assertSee('Kelola Tahun Pelajaran & Semester');
        $response->assertSee('2026/2027');
        $response->assertSee('Semester 1 (Ganjil)');
        $response->assertSee('SEDANG AKTIF');
    }

    public function test_user_can_create_new_academic_year_and_semester(): void
    {
        $year1 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('academic-years.store'), [
            'name' => '2026/2027',
            'semester' => 2,
            'is_active' => 0,
        ]);

        $response->assertRedirect(route('academic-years.index'));
        $this->assertDatabaseHas('academic_years', [
            'name' => '2026/2027',
            'semester' => 2,
            'is_active' => false,
        ]);
    }

    public function test_user_can_switch_active_academic_year(): void
    {
        $sem1 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);

        $sem2 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 2,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->user)->post(route('academic-years.set-active', $sem2->id));

        $response->assertRedirect();
        $this->assertTrue($sem2->fresh()->is_active);
        $this->assertFalse($sem1->fresh()->is_active);
    }

    public function test_grades_history_preserved_across_semesters_without_deleting(): void
    {
        $sem1 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);

        $sem2 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 2,
            'is_active' => false,
        ]);

        $student = Student::create([
            'name' => 'Budi Santoso',
            'nis' => '1001',
            'nisn' => '0012345678',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $sem1->id,
        ]);

        // Simpan nilai Semester 1
        $this->actingAs($this->user)->post(route('grades.store', $student->id), [
            'academic_year_id' => $sem1->id,
            'grades' => [
                $this->subject->id => [
                    'pr' => 85,
                    'uh' => 80,
                    'tertulis' => 90,
                    'praktek' => 88,
                ],
            ],
            'attendance' => ['sakit' => 1, 'izin' => 0, 'alpa' => 0],
        ]);

        $this->assertDatabaseHas('student_grades', [
            'student_id' => $student->id,
            'academic_year_id' => $sem1->id,
            'pr' => 85,
        ]);

        // Beralih ke Semester 2
        $this->actingAs($this->user)->post(route('academic-years.set-active', $sem2->id));

        // Simpan nilai Semester 2 (nilai berbeda)
        $this->actingAs($this->user)->post(route('grades.store', $student->id), [
            'academic_year_id' => $sem2->id,
            'grades' => [
                $this->subject->id => [
                    'pr' => 95,
                    'uh' => 92,
                    'tertulis' => 94,
                    'praktek' => 96,
                ],
            ],
            'attendance' => ['sakit' => 0, 'izin' => 2, 'alpa' => 0],
        ]);

        // Verifikasi kedua rekaman nilai tersimpan di SQLite secara terpisah
        $this->assertDatabaseHas('student_grades', [
            'student_id' => $student->id,
            'academic_year_id' => $sem1->id,
            'pr' => 85,
        ]);
        $this->assertDatabaseHas('student_grades', [
            'student_id' => $student->id,
            'academic_year_id' => $sem2->id,
            'pr' => 95,
        ]);

        // Total nilai di SQLite ada 2 data independen
        $this->assertEquals(2, StudentGrade::where('student_id', $student->id)->count());
    }

    public function test_reports_page_and_pdf_download_button(): void
    {
        $sem1 = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 1,
            'is_active' => true,
        ]);

        $student = Student::create([
            'name' => 'Siti Nurhaliza',
            'nis' => '1002',
            'nisn' => '0012345679',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $sem1->id,
        ]);

        // Halaman Daftar Raport
        $indexResponse = $this->actingAs($this->user)->get(route('reports.index'));
        $indexResponse->assertOk();
        $indexResponse->assertSee('Download PDF (Semua)');
        $indexResponse->assertSee('Cetak Semua');
        $indexResponse->assertSee('Leger Excel');

        // Halaman Cetak Semua dalam format PDF
        $printResponse = $this->actingAs($this->user)->get(route('reports.print-all', ['action' => 'pdf']));
        $printResponse->assertOk();
        $printResponse->assertSee('Panduan Mengunduh Raport Format PDF');
        $printResponse->assertSee('Simpan / Download PDF');
        $printResponse->assertSee('Siti Nurhaliza');
    }
}
