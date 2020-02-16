<?php
namespace App\Models\s_public;
use Illuminate\Database\Eloquent\Model;

class UserLoginHistory extends Model {
    
    protected $table = "public.t_user_login_history";
    public $timestamps = false;
    
    protected $fillable = [
    ];    
}