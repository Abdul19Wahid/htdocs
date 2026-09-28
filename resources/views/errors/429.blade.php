@include('errors.minimal', [
    'title' => 'Too many attempts',
    'message' => "You've made too many requests in a short time. Please wait a minute and try again.",
    'icon' => 'bi-hourglass-split',
    'accent' => '#d97706',
    'accentBg' => 'rgba(217, 119, 6, 0.1)',
])
