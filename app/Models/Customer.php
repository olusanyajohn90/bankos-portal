<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    protected $table = 'customers';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = ['id'];
    protected $hidden = ['portal_password', 'portal_pin', 'remember_token'];
    protected $casts = [
        'portal_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function getAuthPassword(): string
    {
        return $this->portal_password ?? '';
    }

    public function getAuthPasswordName(): string
    {
        return 'portal_password';
    }

    public function accounts()
    {
        return $this->hasMany(Account::class, 'customer_id', 'id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }
}
