@php
    $progreso = $getState();
    $color = 'gray';
    if ($progreso == 100) {
        $color = 'success';
    } elseif ($progreso >= 50) {
        $color = 'warning';
    } elseif ($progreso > 0) {
        $color = 'info';
    }
@endphp

<div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
    <div @class([
        'h-2 rounded-full',
        'bg-success-500' => $color === 'success',
        'bg-warning-500' => $color === 'warning',
        'bg-info-500' => $color === 'info',
        'bg-gray-400' => $color === 'gray',
    ]) style="width: {{ $progreso }}%"></div>
</div>
