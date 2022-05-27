<?php

return [
    'enabled' => env('REFERRALS_SYSTEM', true),
    'levels' => env('REFERRALS_SYSTEM_LEVELS', 5),
    'standard' => explode(',', env('REFERRALS_SYSTEM_STANDARD_PERCENTAGE', '5,3,1,0.5,0.5')),
    'leader' => explode(',', env('REFERRALS_SYSTEM_LEADER_PERCENTAGE', '10,7,5,0.5,0.5'))
];
