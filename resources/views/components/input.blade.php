@props(['id' => null, 'name' => null, 'type' => 'text', 'value' => '', 'required' => false])

@php
    $name = $name ?? $id;
    $id = $id ?? $name;
    $borderColor = $errors->has($name) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500';
@endphp

<input
    {{ $attributes->merge([
        'class' => 'rounded-md shadow-sm ' . $borderColor . ' w-full',
        'type' => $type,
        'id' => $id,
        'name' => $name,
        'value' => old($name, $value),
        'required' => $required
    ]) }}
>

@error($name)
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
@enderror
