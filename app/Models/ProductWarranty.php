<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class ProductWarranty extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'order_id',
        'order_product_uuid',
        'product_id',
        'product_name',
        'product_code',
        'serial_number',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'province_id',
        'district_id',
        'ward_id',
        'purchase_date',
        'activation_date',
        'warranty_months',
        'warranty_end_date',
        'qr_code',
        'product_images',
        'invoice_image',
        'status',
        'customer_note',
        'admin_note',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'activation_date' => 'date',
        'warranty_end_date' => 'date',
        'warranty_months' => 'integer',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function provinces()
    {
        return $this->belongsTo(Province::class, 'province_id', 'code');
    }

    // Accessors
    public function getRemainingDaysAttribute()
    {
        if ($this->status === 'expired') {
            return 0;
        }
        
        $now = now();
        $endDate = $this->warranty_end_date;
        
        if ($endDate < $now) {
            return 0;
        }
        
        return $now->diffInDays($endDate);
    }

    public function getIsExpiredAttribute()
    {
        return $this->warranty_end_date < now();
    }

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Chờ duyệt',
            'active' => 'Đang bảo hành',
            'expired' => 'Hết hạn',
            'rejected' => 'Từ chối',
        ][$this->status] ?? 'Không xác định';
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'warning',
            'active' => 'success',
            'expired' => 'danger',
            'rejected' => 'dark',
        ][$this->status] ?? 'secondary';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeExpiringWithin($query, $days = 30)
    {
        return $query->where('status', 'active')
                     ->whereDate('warranty_end_date', '<=', now()->addDays($days))
                     ->whereDate('warranty_end_date', '>=', now());
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }
}
