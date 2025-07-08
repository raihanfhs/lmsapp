<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseMaterial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCourseMaterial extends Notification
{
    use Queueable;

    protected $course;
    protected $material;

    /**
     * Create a new notification instance.
     *
     * @param \App\Models\Course $course
     * @param \App\Models\CourseMaterial $material
     */
    public function __construct(Course $course, CourseMaterial $material)
    {
        $this->course = $course;
        $this->material = $material;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        // Untuk saat ini, kita akan simpan notifikasi ke database saja.
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Data inilah yang akan disimpan dalam format JSON di kolom 'data' pada tabel notifikasi.
        return [
            'course_id' => $this->course->id,
            'course_name' => $this->course->name,
            'material_id' => $this->material->id,
            'material_title' => $this->material->title,
            'message' => "Materi baru '{$this->material->title}' telah ditambahkan di kursus {$this->course->name}."
        ];
    }
}