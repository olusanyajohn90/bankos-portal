<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PortalBudget extends Model
{
    protected $table = 'portal_budgets';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    public static $categories = [
        'food'          => ['label' => 'Food & Dining',     'emoji' => '🍔', 'color' => '#f59e0b'],
        'transport'     => ['label' => 'Transport',          'emoji' => '🚗', 'color' => '#3b82f6'],
        'bills'         => ['label' => 'Bills & Utilities',  'emoji' => '⚡', 'color' => '#8b5cf6'],
        'entertainment' => ['label' => 'Entertainment',      'emoji' => '🎬', 'color' => '#ec4899'],
        'health'        => ['label' => 'Health & Medical',   'emoji' => '💊', 'color' => '#10b981'],
        'shopping'      => ['label' => 'Shopping',           'emoji' => '🛍️', 'color' => '#f97316'],
        'education'     => ['label' => 'Education',          'emoji' => '📚', 'color' => '#06b6d4'],
        'savings'       => ['label' => 'Savings',            'emoji' => '🏦', 'color' => '#16a34a'],
        'others'        => ['label' => 'Others',             'emoji' => '📦', 'color' => '#6b7280'],
    ];

    // Map transaction descriptions/types to budget categories
    public static function categorizeTransaction(string $description, string $type): string
    {
        $desc = strtolower($description);
        if (str_contains($desc, 'food') || str_contains($desc, 'restaurant') || str_contains($desc, 'eat') || str_contains($desc, 'kfc') || str_contains($desc, 'chicken')) return 'food';
        if (str_contains($desc, 'transport') || str_contains($desc, 'uber') || str_contains($desc, 'bolt') || str_contains($desc, 'fuel') || str_contains($desc, 'bus')) return 'transport';
        if (str_contains($desc, 'bill') || str_contains($desc, 'electricity') || str_contains($desc, 'water') || str_contains($desc, 'dstv') || str_contains($desc, 'airtime') || str_contains($desc, 'data')) return 'bills';
        if (str_contains($desc, 'cinema') || str_contains($desc, 'netflix') || str_contains($desc, 'spotify') || str_contains($desc, 'game')) return 'entertainment';
        if (str_contains($desc, 'hospital') || str_contains($desc, 'pharmacy') || str_contains($desc, 'health') || str_contains($desc, 'doctor')) return 'health';
        if (str_contains($desc, 'shop') || str_contains($desc, 'market') || str_contains($desc, 'store') || str_contains($desc, 'buy')) return 'shopping';
        if (str_contains($desc, 'school') || str_contains($desc, 'tuition') || str_contains($desc, 'course') || str_contains($desc, 'education')) return 'education';
        if ($type === 'repayment') return 'bills';
        return 'others';
    }
}
