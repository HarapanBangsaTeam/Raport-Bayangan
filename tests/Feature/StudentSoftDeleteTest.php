<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentSoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected AcademicYear $year;

    protected Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);
        $this->classroom = Classroom::create(['name' => 'VII-A']);
    }

    public function test_soft_deleting_student_hides_them_from_students_grades_and_reports(): void
    {
        $activeStudent = Student::create([
            'name' => 'Siswa Aktif',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $deletedStudent = Student::create([
            'name' => 'Siswa Akan Dihapus',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        // Soft delete the second student
        $response = $this->actingAs($this->user)->delete(route('students.destroy', $deletedStudent));
        $response->assertRedirect(route('students.index'));

        // Verify in database: deleted_at is not null
        $this->assertSoftDeleted('students', ['id' => $deletedStudent->id]);

        // 1. Check students.index: active student is present, soft-deleted is absent (flush session to avoid flash message)
        $this->flushSession();
        $this->actingAs($this->user)->get(route('students.index'))
            ->assertOk()
            ->assertSee('Siswa Aktif')
            ->assertDontSee('Siswa Akan Dihapus');

        // 2. Check grades.index: soft-deleted is absent
        $this->actingAs($this->user)->get(route('grades.index'))
            ->assertOk()
            ->assertSee('Siswa Aktif')
            ->assertDontSee('Siswa Akan Dihapus');

        // 3. Check reports.index: soft-deleted is absent
        $this->actingAs($this->user)->get(route('reports.index'))
            ->assertOk()
            ->assertSee('Siswa Aktif')
            ->assertDontSee('Siswa Akan Dihapus');

        // 4. Check reports.print-all: soft-deleted is absent
        $this->actingAs($this->user)->get(route('reports.print-all'))
            ->assertOk()
            ->assertSee('Siswa Aktif')
            ->assertDontSee('Siswa Akan Dihapus');

        // 5. Check reports.preview: 404 for soft-deleted student
        $this->actingAs($this->user)->get(route('reports.preview', $deletedStudent->id))
            ->assertNotFound();

        // 6. Check reports.excel: 404 for soft-deleted student
        $this->actingAs($this->user)->get(route('reports.excel', $deletedStudent->id))
            ->assertNotFound();
    }

    public function test_can_view_trashed_students_and_restore_them(): void
    {
        $student = Student::create([
            'name' => 'Siswa Pulih',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $subject = Subject::create(['name' => 'Matematika', 'order_number' => 1, 'is_active' => true]);
        StudentGrade::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $this->year->id,
            'pr' => 88,
        ]);
        Attendance::create([
            'student_id' => $student->id,
            'academic_year_id' => $this->year->id,
            'sakit' => 1,
        ]);

        // Soft delete
        $student->delete();
        $this->assertSoftDeleted('students', ['id' => $student->id]);

        // Trashed tab shows the student
        $this->actingAs($this->user)->get(route('students.index', ['status' => 'trashed']))
            ->assertOk()
            ->assertSee('Siswa Pulih');

        // Restore the student
        $response = $this->actingAs($this->user)->post(route('students.restore', $student->id));
        $response->assertRedirect(route('students.index', ['status' => 'trashed']));

        // Verify restored in database
        $this->assertNotSoftDeleted('students', ['id' => $student->id]);

        // Appears back in active list and reports
        $this->actingAs($this->user)->get(route('students.index'))
            ->assertOk()
            ->assertSee('Siswa Pulih');

        $this->actingAs($this->user)->get(route('reports.preview', $student->id))
            ->assertOk()
            ->assertSee('Siswa Pulih');
    }

    public function test_can_force_delete_student_permanently(): void
    {
        $student = Student::create([
            'name' => 'Siswa Dihapus Permanen',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $subject = Subject::create(['name' => 'IPA', 'order_number' => 1, 'is_active' => true]);
        StudentGrade::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $this->year->id,
            'pr' => 75,
        ]);

        // Soft delete
        $student->delete();

        // Force delete
        $response = $this->actingAs($this->user)->delete(route('students.force-delete', $student->id));
        $response->assertRedirect(route('students.index', ['status' => 'trashed']));

        // Completely removed from database
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
        $this->assertDatabaseMissing('student_grades', ['student_id' => $student->id]);
    }
}
