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

class TeacherExperienceFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected AcademicYear $year;

    protected Classroom $classroom;

    protected Subject $subject1;

    protected Subject $subject2;

    protected Student $student1;

    protected Student $student2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => '1',
            'is_active' => true,
        ]);
        $this->classroom = Classroom::create(['name' => 'VII-A']);

        SchoolSetting::create([
            'nama_sekolah' => 'SMP HARAPAN BANGSA KARAWANG',
            'alamat' => 'Jl. R.E. Martadinata no. 8',
            'judul_laporan' => 'Laporan Hasil Belajar',
        ]);

        $this->subject1 = Subject::create([
            'name' => 'Matematika',
            'order_number' => 1,
            'is_active' => true,
        ]);
        $this->subject2 = Subject::create([
            'name' => 'Bahasa Indonesia',
            'order_number' => 2,
            'is_active' => true,
        ]);

        $this->student1 = Student::create([
            'name' => 'Ahmad Dani',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $this->student2 = Student::create([
            'name' => 'Bintang Pratama',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);
    }

    public function test_grades_index_shows_completeness_badges_and_mode_tabs(): void
    {
        // Student 1 has all 2 subjects filled
        StudentGrade::create([
            'student_id' => $this->student1->id,
            'subject_id' => $this->subject1->id,
            'academic_year_id' => $this->year->id,
            'pr' => 80,
        ]);
        StudentGrade::create([
            'student_id' => $this->student1->id,
            'subject_id' => $this->subject2->id,
            'academic_year_id' => $this->year->id,
            'uh' => 85,
        ]);

        $response = $this->actingAs($this->user)->get(route('grades.index'));

        $response->assertStatus(200);
        $response->assertSee('Mode per Mata Pelajaran');
        $response->assertSee('Lengkap (2/2)');
        $response->assertSee('Belum Diisi');
    }

    public function test_grades_edit_shows_quick_navigation_and_template_notes(): void
    {
        $response = $this->actingAs($this->user)->get(route('grades.edit', $this->student1->id));

        $response->assertStatus(200);
        $response->assertSee('Siswa Berikutnya');
        $response->assertSee('Pilihan Cepat Template Catatan:');
        $response->assertSee('Simpan & Lanjut', false);
    }

    public function test_save_and_next_saves_grades_and_redirects_to_next_student(): void
    {
        $response = $this->actingAs($this->user)->post(route('grades.store', $this->student1->id), [
            'action' => 'save_and_next',
            'grades' => [
                $this->subject1->id => [
                    'pr' => '90',
                    'uh' => '95',
                    'tertulis' => '90',
                    'praktek' => '90',
                ],
            ],
            'attendance' => [
                'sakit' => 0,
                'izin' => 0,
                'alpa' => 0,
                'note' => 'Prestasi sangat baik',
            ],
        ]);

        // Should redirect to student 2
        $response->assertRedirect(route('grades.edit', $this->student2->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('student_grades', [
            'student_id' => $this->student1->id,
            'subject_id' => $this->subject1->id,
            'pr' => 90,
        ]);
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student1->id,
            'note' => 'Prestasi sangat baik',
        ]);
    }

    public function test_save_and_next_on_last_student_redirects_to_grades_index(): void
    {
        $response = $this->actingAs($this->user)->post(route('grades.store', $this->student2->id), [
            'action' => 'save_and_next',
            'grades' => [
                $this->subject1->id => ['pr' => '85'],
            ],
        ]);

        $response->assertRedirect(route('grades.index'));
        $response->assertSessionHas('success');
    }

    public function test_by_subject_page_loads_and_can_batch_store_grades(): void
    {
        $response = $this->actingAs($this->user)->get(route('grades.by-subject', ['subject_id' => $this->subject1->id]));

        $response->assertStatus(200);
        $response->assertSee($this->subject1->name);
        $response->assertSee($this->student1->name);
        $response->assertSee($this->student2->name);

        // Batch save
        $postResponse = $this->actingAs($this->user)->post(route('grades.store-by-subject'), [
            'subject_id' => $this->subject1->id,
            'grades' => [
                $this->student1->id => [
                    'pr' => '88',
                    'uh' => '90',
                    'tertulis' => '92',
                    'praktek' => '94',
                ],
                $this->student2->id => [
                    'pr' => '78',
                    'uh' => '80',
                    'tertulis' => '82',
                    'praktek' => '84',
                ],
            ],
        ]);

        $postResponse->assertRedirect(route('grades.by-subject', ['subject_id' => $this->subject1->id]));
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('student_grades', [
            'student_id' => $this->student1->id,
            'subject_id' => $this->subject1->id,
            'pr' => 88,
        ]);
        $this->assertDatabaseHas('student_grades', [
            'student_id' => $this->student2->id,
            'subject_id' => $this->subject1->id,
            'pr' => 78,
        ]);
    }

    public function test_export_leger_downloads_excel(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.export-leger'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains((string) $response->headers->get('content-type'), 'spreadsheetml')
        );
    }
}
