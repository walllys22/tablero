@props(['align' => 'right', 'width' => '48', 'contentClasses' => ''])

@php
    $alignment = $align === 'left' ? '' : 'dropdown-menu-end';
@endphp

<div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        {{ $trigger }}
    </button>

    <div {{ $attributes->merge(['class' => "dropdown-menu {$alignment} {$contentClasses}"]) }}>
        {{ $content }}
    </div>
</div>
