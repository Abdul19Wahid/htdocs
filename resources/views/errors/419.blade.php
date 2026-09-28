@include('errors.minimal', [
    'title' => 'Your session expired',
    'message' => "For your security, sessions time out after a period of inactivity. Please log in again to continue.",
    'icon' => 'bi-clock-history',
    'accent' => '#2563eb',
    'accentBg' => 'rgba(37, 99, 235, 0.1)',
])
