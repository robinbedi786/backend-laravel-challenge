<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsSource extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_key',
        'base_url',
        'api_endpoint',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_preferences');
    }
} 