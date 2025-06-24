<?php

namespace App\Policies;

use App\Models\CourseMaterial;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CourseMaterialPolicy
{
    /**
     * Tentukan apakah user bisa membuat materi baru.
     * Aturan: User bisa membuat materi jika dia adalah guru dari course tersebut.
     */
    public function create(User $user, \App\Models\Course $course): bool
    {
        // Cek apakah user ini ada di dalam daftar guru untuk course ini.
        return $user->teachingCourses()->where('course_id', $course->id)->exists();
    }

    /**
     * Tentukan apakah user bisa meng-update materi.
     * Aturan: User bisa update materi jika dia adalah guru dari course tempat materi itu berada.
     */
    public function update(User $user, CourseMaterial $courseMaterial): bool
    {
        // Cek apakah user ini adalah guru dari course_id milik materi ini.
        return $user->teachingCourses()->where('course_id', $courseMaterial->course_id)->exists();
    }

    /**
     * Tentukan apakah user bisa menghapus materi.
     * Aturan: Sama seperti update.
     */
    public function delete(User $user, CourseMaterial $courseMaterial): bool
    {
        // Logikanya sama dengan update.
        return $this->update($user, $courseMaterial);
    }

    // Anda bisa menambahkan method lain seperti view() atau restore() jika perlu
}