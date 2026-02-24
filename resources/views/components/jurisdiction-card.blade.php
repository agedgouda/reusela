@props([
    'editable' => false,
    'variant' => 'default',
])
@php
    $bgColor = $variant === 'violation'
        ? 'bg-[#9adbe8] border-[#9adbe8]'
        : 'bg-[#e4fbff] border-[#e4fbff]';
@endphp

<div {{ $attributes->merge([
    'class' => "$bgColor border rounded-[12px] p-8 text-[16px] leading-[24px] text-[#1E1E1E] prose max-w-none rounded-[20px] p-[72px] flex flex-col gap-[36px] items-start justify-start"
]) }}
x-data="{ expanded: false }"
x-init="expanded = true"
>
    <div x-show="expanded" x-collapse.duration.500ms>
        {{ $slot }}
    </div>
</div>
