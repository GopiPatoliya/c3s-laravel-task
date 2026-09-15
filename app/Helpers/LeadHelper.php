<?php

if (!function_exists('leadStatusBadge')) {
    function leadStatusBadge($status)
    {
        $badges = [
            'new' => '<span class="badge bg-primary">New</span>',
            'contacted' => '<span class="badge bg-info">Contacted</span>',
            'converted' => '<span class="badge bg-success">Converted</span>',
            'lost' => '<span class="badge bg-danger">Lost</span>',
        ];

        return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
    }
}
