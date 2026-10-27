<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class ReportCardDynamicSubjectTest extends TestCase
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

    public function test_preview_displays_newly_added_subject_even_without_grades(): void
    {
        $student = Student::create([
            'name' => 'Budi Santoso',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $subject1 = Subject::create([
            'name' => 'Bahasa Sunda Asli',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $subject2 = Subject::create([
            'name' => 'Robotika Dasar',
            'order_number' => 2,
            'is_active' => true,
        ]);

        // Only subject1 has grades entered
        $student->grades()->create([
            'subject_id' => $subject1->id,
            'academic_year_id' => $this->year->id,
            'pr' => 85,
            'uh' => 90,
            'tertulis' => 88,
            'praktek' => 92,
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.preview', $student->id));

        $response->assertStatus(200);
        // Subject 1 with grade
        $response->assertSee('Bahasa Sunda Asli');
        $response->assertSee('85');
        // Subject 2 without grade must also be visible with '-'
        $response->assertSee('Robotika Dasar');
    }

    public function test_preview_removes_deleted_subject(): void
    {
        $student = Student::create([
            'name' => 'Siti Aminah',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $activeSubject = Subject::create([
            'name' => 'Matematika Terapan',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $deletedSubject = Subject::create([
            'name' => 'Mapel Yang Dihapus',
            'order_number' => 2,
            'is_active' => true,
        ]);

        // Delete the subject
        $this->actingAs($this->user)->delete(route('subjects.destroy', $deletedSubject->id));

        $response = $this->actingAs($this->user)->get(route('reports.preview', $student->id));

        $response->assertStatus(200);
        $response->assertSee('Matematika Terapan');
        $response->assertDontSee('Mapel Yang Dihapus');
    }

    public function test_excel_export_includes_active_subjects_and_grades(): void
    {
        $student = Student::create([
            'name' => 'Dewi Lestari',
            'nis' => '998877',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        $subject = Subject::create([
            'name' => 'Pendidikan Karakter Khusus',
            'order_number' => 1,
            'is_active' => true,
        ]);

        $student->grades()->create([
            'subject_id' => $subject->id,
            'academic_year_id' => $this->year->id,
            'pr' => 95,
            'uh' => 90,
            'tertulis' => 92,
            'praktek' => 96,
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.excel', $student->id));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Stream and parse output
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $tempFile = tempnam(sys_get_temp_dir(), 'raport_test_').'.xlsx';
        file_put_contents($tempFile, $content);

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($tempFile);

        $wsData = $spreadsheet->getSheetByName('DATA');
        $this->assertNotNull($wsData);
        $this->assertEquals('Pendidikan Karakter Khusus', $wsData->getCell('B15')->getValue());

        $wsRaport = $spreadsheet->getSheetByName('RAPORT');
        $this->assertNotNull($wsRaport);
        $this->assertEquals('Pendidikan Karakter Khusus', $wsRaport->getCell('B15')->getCalculatedValue());
        $this->assertEquals(95, $wsRaport->getCell('D15')->getCalculatedValue());

        @unlink($tempFile);
    }

    public function test_excel_export_handles_more_than_17_subjects(): void
    {
        $student = Student::create([
            'name' => 'Agus Keren',
            'nis' => '112233',
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->year->id,
            'status' => 'Aktif',
        ]);

        for ($i = 1; $i <= 19; $i++) {
            Subject::create([
                'name' => "Mata Pelajaran Test {$i}",
                'order_number' => $i,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->user)->get(route('reports.excel', $student->id));
        $response->assertStatus(200);

        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        $tempFile = tempnam(sys_get_temp_dir(), 'raport_test_').'.xlsx';
        file_put_contents($tempFile, $content);

        $reader = IOFactory::createReader('Xlsx');
        $spreadsheet = $reader->load($tempFile);
        $wsRaport = $spreadsheet->getSheetByName('RAPORT');

        $this->assertEquals('Mata Pelajaran Test 18', $wsRaport->getCell('B32')->getCalculatedValue());
        $this->assertEquals('Mata Pelajaran Test 19', $wsRaport->getCell('B33')->getCalculatedValue());

        @unlink($tempFile);
    }

    public function test_can_move_subject_up(): void
    {
        $subj1 = Subject::create(['name' => 'Bahasa Indonesia', 'order_number' => 1, 'is_active' => true]);
        $subj2 = Subject::create(['name' => 'Matematika', 'order_number' => 2, 'is_active' => true]);
        $subj3 = Subject::create(['name' => 'Seni Tari', 'order_number' => 3, 'is_active' => true]);

        // Move subj3 (Seni Tari) up
        $response = $this->actingAs($this->user)->post(route('subjects.move-up', $subj3->id));
        $response->assertRedirect(route('subjects.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(1, $subj1->fresh()->order_number);
        $this->assertEquals(2, $subj3->fresh()->order_number);
        $this->assertEquals(3, $subj2->fresh()->order_number);
    }

    public function test_can_move_subject_down(): void
    {
        $subj1 = Subject::create(['name' => 'Bahasa Indonesia', 'order_number' => 1, 'is_active' => true]);
        $subj2 = Subject::create(['name' => 'Matematika', 'order_number' => 2, 'is_active' => true]);
        $subj3 = Subject::create(['name' => 'Seni Tari', 'order_number' => 3, 'is_active' => true]);

        // Move subj1 (Bahasa Indonesia) down
        $response = $this->actingAs($this->user)->post(route('subjects.move-down', $subj1->id));
        $response->assertRedirect(route('subjects.index'));
        $response->assertSessionHas('success');

        $this->assertEquals(1, $subj2->fresh()->order_number);
        $this->assertEquals(2, $subj1->fresh()->order_number);
        $this->assertEquals(3, $subj3->fresh()->order_number);
    }

    public function test_store_automatically_assigns_next_order_number(): void
    {
        Subject::create(['name' => 'Mapel 1', 'order_number' => 1, 'is_active' => true]);
        Subject::create(['name' => 'Mapel 2', 'order_number' => 2, 'is_active' => true]);

        // Store subject without specifying order_number
        $response = $this->actingAs($this->user)->post(route('subjects.store'), [
            'name' => 'Mapel 3 Baru',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('subjects.index'));
        $newSubject = Subject::where('name', 'Mapel 3 Baru')->first();
        $this->assertNotNull($newSubject);
        $this->assertEquals(3, $newSubject->order_number);
    }
}
