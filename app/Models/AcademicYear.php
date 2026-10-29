<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'semester' => 'integer',
    ];

    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function getSemesterNameAttribute(): string
    {
        return $this->semester === 2 ? 'Genap' : 'Ganjil';
    }

    public function getFullPeriodAttribute(): string
    {
        return "{$this->name} - Semester {$this->semester} ({$this->semester_name})";
    }
}
