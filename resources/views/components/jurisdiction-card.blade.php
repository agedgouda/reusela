@props([
    'editable' => false,
    'variant' => 'default',
])
@php
    $bgColor = $variant === 'violation'
        ? 'bg-[#9adbe8] border-[#9adbe8]'
        : 'bg-[#e4fbff] border-[#e4fbff]';
@endphp

<div {{ $attributes->class([
    "$bgColor border rounded-[12px] flex flex-col gap-[36px] items-start justify-start md:prose max-w-none text-[16px] leading-[24px] text-[#1E1E1E]",

    // mobile width/padding
    "w-[350px] p-9",

    // desktop overrides
    "md:w-full md:p-[72px]",
]) }}
x-data="{ expanded: false }"
x-init="expanded = true"
>
    <div x-show="expanded" x-collapse.duration.500ms>
        {{ $slot }}
    </div>
</div>
