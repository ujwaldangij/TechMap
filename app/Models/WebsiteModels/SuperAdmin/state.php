<?php

namespace App\Models\WebsiteModels\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class state extends Model
{
    use HasFactory;
    protected $table = "state";
    protected $primaryKey = "id";
    protected $fillable = [
        'name','upload_report','created_at','updated_at','created_id','circle_image','final_report','confirm','specialty'
    ];
}
