<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PortalDispute extends Model
{
    protected $table = 'portal_disputes';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public static $types = [
        'unauthorized_transaction' => 'Unauthorized Transaction',
        'wrong_amount'             => 'Wrong Amount Charged',
        'double_charge'            => 'Double Charge',
        'failed_transfer'          => 'Failed Transfer — Funds Deducted',
        'atm_dispute'              => 'ATM Dispute',
        'card_fraud'               => 'Card Fraud',
        'other'                    => 'Other',
    ];

    public static $statusColors = [
        'open'          => ['#d97706', '#fffbeb', '#fde68a'],
        'investigating' => ['#2563eb', '#eff6ff', '#bfdbfe'],
        'resolved'      => ['#16a34a', '#f0fdf4', '#bbf7d0'],
        'rejected'      => ['#dc2626', '#fef2f2', '#fecaca'],
        'escalated'     => ['#7c3aed', '#f5f3ff', '#ddd6fe'],
    ];
}
