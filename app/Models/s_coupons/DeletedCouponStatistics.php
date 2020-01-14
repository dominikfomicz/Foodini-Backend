<?php
namespace App\Models\s_coupons;
use Illuminate\Database\Eloquent\Model;

class DeletedCouponStatistics extends Model {
    
    protected $table = "s_coupons.t_deleted_coupon_statistics";
    public $timestamps = false;
    
    protected $fillable = [
    ];    
}