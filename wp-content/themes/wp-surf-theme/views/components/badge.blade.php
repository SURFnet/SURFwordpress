@props(['secondary' => false])
@props(['clear' => false])

<p class="badge {{ $secondary ? 'badge--secondary' : '' }} {{ $clear ? 'badge--clear' : '' }}">{{ $slot }}</p>
