@props(['editable' => false])

<div {{ $attributes->merge([
    'class' => "jurisdiction-card rich-text prose max-w-none dark:prose-invert " . ($editable ? 'jurisdiction-card-editable' : '')
]) }}
x-data="{ expanded: false }"
x-init="expanded = true"
>
    <div x-show="expanded" x-collapse.duration.500ms>
        {{ $slot }}
    </div>
</div>
