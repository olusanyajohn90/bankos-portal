<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Signed Statement Fee (NGN)
    |--------------------------------------------------------------------------
    | Amount charged per official bank-signed account statement request.
    | Set SIGNED_STATEMENT_FEE in .env to override.
    */
    'signed_statement_fee' => (int) env('SIGNED_STATEMENT_FEE', 20),

    /*
    |--------------------------------------------------------------------------
    | Tawk.to Live Chat
    |--------------------------------------------------------------------------
    | Set TAWK_PROPERTY_ID and TAWK_WIDGET_ID in .env to enable the Tawk.to
    | live chat widget. Leave blank to disable.
    | Get your IDs from https://www.tawk.to → Administration → Chat Widget
    */
    'tawk_property_id' => env('TAWK_PROPERTY_ID', ''),
    'tawk_widget_id'   => env('TAWK_WIDGET_ID', 'default'),
];
