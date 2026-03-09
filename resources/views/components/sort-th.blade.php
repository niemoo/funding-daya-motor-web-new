@props(['column', 'label', 'currentSort', 'currentDir'])

@php
    $isActive = $currentSort === $column;
    $nextDir = $isActive && $currentDir === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir, 'page' => 1]);
@endphp

<th
    class="text-[11px] font-semibold uppercase tracking-wide text-slate-400 px-4 py-3 text-left bg-slate-50 border-b border-slate-100">
    <a href="{{ $url }}"
        class="inline-flex items-center gap-1.5 hover:text-brand-600 transition-colors {{ $isActive ? 'text-brand-600' : '' }}">
        {{ $label }}
        <span class="flex flex-col gap-px leading-none">
            <span class="{{ $isActive && $currentDir === 'asc' ? 'text-brand-600' : 'text-slate-300' }}"
                style="font-size:8px; line-height:1">▲</span>
            <span class="{{ $isActive && $currentDir === 'desc' ? 'text-brand-600' : 'text-slate-300' }}"
                style="font-size:8px; line-height:1">▼</span>
        </span>
    </a>
</th>
