<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentSubjectManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_student_edit_page(): void
    {
        $classroom = Classroom::create(['name' => '1A']);
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $student = Student::create([
            'name' => 'John Doe',
            'classroom_id' => $classroom->id,
            'academic_year_id' => $year->id,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($this->user)->get(route('students.edit', $student->id));

        $response->assertStatus(200);
        $response->assertSee('John Doe');
    }

    public function test_can_update_student(): void
    {
        $classroom = Classroom::create(['name' => '1A']);
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $student = Student::create([
            'name' => 'John Doe',
            'classroom_id' => $classroom->id,
            'academic_year_id' => $year->id,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($this->user)->put(route('students.update', $student->id), [
            'name' => 'John Updated',
            'nis' => '12345',
            'classroom_id' => $classroom->id,
            'academic_year_id' => $year->id,
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'John Updated',
            'nis' => '12345',
        ]);
    }

    public function test_can_view_subject_edit_page(): void
    {
        $subject = Subject::create([
            'name' => 'Matematika',
            'code' => 'MTK',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->get(route('subjects.edit', $subject->id));

        $response->assertStatus(200);
        $response->assertSee('Matematika');
    }

    public function test_can_update_subject(): void
    {
        $subject = Subject::create([
            'name' => 'Matematika',
            'code' => 'MTK',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->put(route('subjects.update', $subject->id), [
            'name' => 'Matematika Wajib',
            'code' => 'MTKW',
            'order_number' => 2,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Matematika Wajib',
            'code' => 'MTKW',
            'order_number' => 2,
        ]);
    }

    public function test_can_create_student(): void
    {
        $classroom = Classroom::create(['name' => '1A']);
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->post(route('students.store'), [
            'name' => 'Jane Doe',
            'nis' => '102031',
            'classroom_id' => $classroom->id,
            'academic_year_id' => $year->id,
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('students.index'));
        $this->assertDatabaseHas('students', [
            'name' => 'Jane Doe',
            'nis' => '102031',
        ]);
    }

    public function test_can_delete_student(): void
    {
        $classroom = Classroom::create(['name' => '1A']);
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $student = Student::create([
            'name' => 'John Doe',
            'classroom_id' => $classroom->id,
            'academic_year_id' => $year->id,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($this->user)->delete(route('students.destroy', $student->id));

        $response->assertRedirect(route('students.index'));
        $this->assertSoftDeleted('students', [
            'id' => $student->id,
        ]);
    }

    public function test_can_create_subject(): void
    {
        $response = $this->actingAs($this->user)->post(route('subjects.store'), [
            'name' => 'IPA',
            'code' => 'IPA',
            'order_number' => 3,
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subjects', [
            'name' => 'IPA',
            'code' => 'IPA',
            'order_number' => 3,
        ]);
    }

    public function test_can_delete_subject(): void
    {
        $subject = Subject::create([
            'name' => 'Seni Budaya',
            'code' => 'SB',
            'order_number' => 4,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->user)->delete(route('subjects.destroy', $subject->id));

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
        ]);
    }

    public function test_report_preview_shows_sum_and_average(): void
    {
        $classroom = Classroom::create(['name' => '1A']);
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $student = Student::create([
            'name' => 'John Doe',
            'classroom_id' => $classroom->id,
            'academic_year_id' => $year->id,
            'status' => 'Aktif',
        ]);

        $subject = Subject::create([
            'name' => 'Matematika',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $student->grades()->create([
            'subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'pr' => 80,
            'uh' => 90,
            'tertulis' => 85,
            'praktek' => 95,
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.preview', $student->id));

        $response->assertStatus(200);
        $response->assertSee('RATA-RATA');
        $response->assertSee('RATA-RATA KESELURUHAN');
    }
}
