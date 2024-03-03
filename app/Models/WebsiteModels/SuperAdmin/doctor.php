<?php

namespace App\Models\WebsiteModels\SuperAdmin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class doctor extends Model
{
    use HasFactory;
    protected $table = "doctor";
    protected $primaryKey = "id";
    public $timestamps = false;
}
