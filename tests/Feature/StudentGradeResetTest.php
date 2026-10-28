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

class StudentGradeResetTest extends TestCase
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
            'name' => 'Budi Santoso',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $this->student2 = Student::create([
            'name' => 'Siti Nurhaliza',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        // Berikan nilai untuk student 1
        StudentGrade::create([
            'student_id' => $this->student1->id,
            'subject_id' => $this->subject1->id,
            'academic_year_id' => $this->year->id,
            'pr' => 85,
            'uh' => 90,
            'tertulis' => 88,
            'praktek' => 92,
        ]);
        StudentGrade::create([
            'student_id' => $this->student1->id,
            'subject_id' => $this->subject2->id,
            'academic_year_id' => $this->year->id,
            'pr' => 80,
            'uh' => 85,
            'tertulis' => 80,
            'praktek' => 85,
        ]);

        // Berikan nilai untuk student 2
        StudentGrade::create([
            'student_id' => $this->student2->id,
            'subject_id' => $this->subject1->id,
            'academic_year_id' => $this->year->id,
            'pr' => 75,
            'uh' => 78,
            'tertulis' => 80,
            'praktek' => 82,
        ]);

        // Attendance
        Attendance::create([
            'student_id' => $this->student1->id,
            'academic_year_id' => $this->year->id,
            'sakit' => 2,
            'izin' => 1,
            'alpa' => 0,
            'note' => 'Bagus sekali',
        ]);
        Attendance::create([
            'student_id' => $this->student2->id,
            'academic_year_id' => $this->year->id,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 1,
            'note' => 'Pertahankan',
        ]);
    }

    public function test_grades_index_displays_delete_all_grades_button_when_grades_exist(): void
    {
        $response = $this->actingAs($this->user)->get(route('grades.index'));

        $response->assertStatus(200);
        $response->assertSee('Kosongkan Semua Nilai');
    }

    public function test_authenticated_user_can_delete_all_grades_and_reset_attendance(): void
    {
        $this->assertDatabaseCount('student_grades', 3);
        $this->assertDatabaseCount('attendances', 2);

        $response = $this->actingAs($this->user)->delete(route('grades.destroy-all'), [
            'reset_attendance' => '1',
        ]);

        $response->assertRedirect(route('grades.index'));
        $response->assertSessionHas('success');

        // Nilai harus kosong
        $this->assertDatabaseCount('student_grades', 0);
        // Absensi juga harus kosong
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_authenticated_user_can_delete_all_grades_without_resetting_attendance(): void
    {
        $this->assertDatabaseCount('student_grades', 3);
        $this->assertDatabaseCount('attendances', 2);

        $response = $this->actingAs($this->user)->delete(route('grades.destroy-all'), [
            'reset_attendance' => '0',
        ]);

        $response->assertRedirect(route('grades.index'));
        $response->assertSessionHas('success');

        // Nilai harus kosong
        $this->assertDatabaseCount('student_grades', 0);
        // Absensi tetap ada
        $this->assertDatabaseCount('attendances', 2);
    }

    public function test_unauthenticated_user_cannot_delete_all_grades(): void
    {
        $response = $this->delete(route('grades.destroy-all'));

        $response->assertRedirect(route('login'));
        $this->assertDatabaseCount('student_grades', 3);
    }

    public function test_authenticated_user_can_delete_single_student_grades(): void
    {
        $this->assertDatabaseCount('student_grades', 3);

        $response = $this->actingAs($this->user)->delete(route('grades.destroy-student', $this->student1->id), [
            'reset_attendance' => '1',
        ]);

        $response->assertRedirect(route('grades.edit', $this->student1->id));
        $response->assertSessionHas('success');

        // Student 1 grades should be gone
        $this->assertDatabaseMissing('student_grades', ['student_id' => $this->student1->id]);
        $this->assertDatabaseMissing('attendances', ['student_id' => $this->student1->id]);

        // Student 2 grades and attendance should remain intact
        $this->assertDatabaseHas('student_grades', ['student_id' => $this->student2->id]);
        $this->assertDatabaseHas('attendances', ['student_id' => $this->student2->id]);
        $this->assertDatabaseCount('student_grades', 1);
        $this->assertDatabaseCount('attendances', 1);
    }
}
