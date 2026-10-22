<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Satu Trainer memiliki satu profil
    public function trainerProfile()
    {
        return $this->hasOne(TrainerProfile::class);
    }

    // Satu Trainer dapat memiliki banyak transaksi rental
    public function rentals()
    {
        return $this->hasMany(Rental::class, 'trainer_id');
    }

    // Admin dapat memproses banyak pengembalian
    public function processedRentals()
    {
        return $this->hasMany(Rental::class, 'processed_by_admin_id');
    }

    // Mengecek apakah user adalah Admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Mengecek apakah user adalah Trainer
    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }
}
