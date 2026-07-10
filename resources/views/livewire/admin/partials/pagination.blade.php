@if ($paginator->hasPages())
    <nav>
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <button disabled class="nx-btn-icon" style="opacity: 0.3;">←</button>
        @else
            <button wire:click="previousPage" class="nx-btn-icon">←</button>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding: 4px 8px; color: var(--nx-text-muted);">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button class="nx-btn-icon" style="background: var(--nx-accent); color: white; border-color: var(--nx-accent);">{{ $page }}</button>
                    @else
                        <button wire:click="gotoPage({{ $page }})" class="nx-btn-icon">{{ $page }}</button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" class="nx-btn-icon">→</button>
        @else
            <button disabled class="nx-btn-icon" style="opacity: 0.3;">→</button>
        @endif
    </nav>
@endif
