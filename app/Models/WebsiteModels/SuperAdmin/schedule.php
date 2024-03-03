<?php

namespace App\Models\WebsiteModels\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class schedule extends Model
{
    use HasFactory;
    protected $table = "schedule";
    protected $primaryKey = "id";
    protected $fillable = [
        'doctor_id','status','agent','result','upload_report','created_at','updated_at','user_id'
    ];
}
