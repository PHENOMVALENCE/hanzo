<?php

return [
    /*
    | Clearing cost estimates by destination (USD).
    | Key: city/country slug or name. Value: min, max range.
    */
    'destinations' => [
        'dar es salaam' => ['min' => 150, 'max' => 400],
        'nairobi' => ['min' => 120, 'max' => 350],
        'kampala' => ['min' => 100, 'max' => 300],
        'kigali' => ['min' => 80, 'max' => 250],
        'other' => ['min' => 100, 'max' => 300],
    ],
];
