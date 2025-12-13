@props(['disabled' => false, 'options' => [], 'placeholder' => 'Select an option'])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
    <option value="">{{ $placeholder }}</option>
    @foreach($options as $value => $option)
        <option value="{{ $option->id }}">{{ $option->name }}</option>
    @endforeach
</select>