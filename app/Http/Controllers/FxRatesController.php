<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class FxRatesController extends Controller
{
    public function index()
    {
        // Use exchange_rates table if available, else simulate
        $rates = [];
        try {
            $rows = \Illuminate\Support\Facades\DB::table('exchange_rates')
                ->where('base_currency', 'NGN')
                ->orWhere('quote_currency', 'NGN')
                ->get();
            foreach ($rows as $row) {
                if ($row->base_currency === 'NGN') {
                    $rates[$row->quote_currency] = ['buy' => $row->buy_rate ?? $row->rate, 'sell' => $row->sell_rate ?? $row->rate, 'mid' => $row->rate];
                } elseif ($row->quote_currency === 'NGN') {
                    $rates[$row->base_currency] = ['buy' => 1 / ($row->sell_rate ?? $row->rate), 'sell' => 1 / ($row->buy_rate ?? $row->rate), 'mid' => 1 / $row->rate];
                }
            }
        } catch (\Exception $e) {}

        // Fallback / supplement with simulated rates (PBOC-style mid-rates)
        $defaults = [
            'USD' => ['buy' => 1580.00, 'sell' => 1620.00, 'mid' => 1600.00, 'flag' => '🇺🇸', 'name' => 'US Dollar'],
            'GBP' => ['buy' => 1990.00, 'sell' => 2040.00, 'mid' => 2015.00, 'flag' => '🇬🇧', 'name' => 'British Pound'],
            'EUR' => ['buy' => 1700.00, 'sell' => 1740.00, 'mid' => 1720.00, 'flag' => '🇪🇺', 'name' => 'Euro'],
            'CAD' => ['buy' => 1130.00, 'sell' => 1155.00, 'mid' => 1142.00, 'flag' => '🇨🇦', 'name' => 'Canadian Dollar'],
            'GHS' => ['buy' => 100.00,  'sell' => 104.00,  'mid' => 102.00,  'flag' => '🇬🇭', 'name' => 'Ghanaian Cedi'],
            'KES' => ['buy' => 11.50,   'sell' => 12.10,   'mid' => 11.80,   'flag' => '🇰🇪', 'name' => 'Kenyan Shilling'],
            'ZAR' => ['buy' => 84.00,   'sell' => 87.00,   'mid' => 85.50,   'flag' => '🇿🇦', 'name' => 'South African Rand'],
            'CNY' => ['buy' => 219.00,  'sell' => 224.00,  'mid' => 221.50,  'flag' => '🇨🇳', 'name' => 'Chinese Yuan'],
        ];

        foreach ($defaults as $code => $data) {
            if (!isset($rates[$code])) $rates[$code] = $data;
            else $rates[$code] = array_merge($data, $rates[$code]);
        }

        return view('rates.index', compact('rates'));
    }
}
