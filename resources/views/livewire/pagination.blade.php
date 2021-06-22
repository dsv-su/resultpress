@if ($paginator->hasPages())
    <ul class="pagination" role="navigation">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="page-link" aria-hidden="true">&lsaquo;</span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="#" wire:click="setPage('{{$paginator->previousPageURL()}}')" rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach($elements as $element)
            {{-- "Three Dots" Seperator --}}
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array of Links --}}
            @if (is_array($element))
                @foreach($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{$page}}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="#" wire:click="setPage('{{ $url }}')">{{ $page }}</a> </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="#" rel="next" wire:click="setPage('{{ $paginator->nextPageUrl() }}')" aria-label="@lang('pagination.next')">&rsaquo;</a>
            </li>
        @else
        <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
            <span class="page-link" aria-hidden="true">&rsaquo;</span>
        </li>
        @endif
    </ul>
@endif
