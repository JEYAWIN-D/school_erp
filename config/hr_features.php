<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HR Module Feature Flags (Temporary Demo Disablement)
    |--------------------------------------------------------------------------
    |
    | Temporary demo disablement. Set to true to re-enable the feature.
    | Do not delete underlying functionality.
    |
    | When set to false:
    | - The corresponding Quick Action card on the HR dashboard is rendered
    |   in a disabled, non-interactive state with a "Temporarily Disabled" badge.
    | - When 'events' is false, the "Upcoming Staff Events" dashboard section is hidden.
    |
    | When set to true:
    | - The features, links, and dashboard sections immediately become fully active again.
    |
    */

    'leave_approval' => true,
    'reports'        => true,
    'events'         => true,
];
