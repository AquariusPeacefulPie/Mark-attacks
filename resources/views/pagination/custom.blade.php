@if ($paginator->hasPages())
    <div class="flex justify-between items-center bg-blue-600 text-white p-2">
        <div class="flex items-center">
            <span class="mr-2">Affichage des utilisateurs {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }} sur {{ $paginator->total() }}</span>
            <span class="mx-2">|</span>
            <span>Page {{ $paginator->currentPage() }} sur {{ $paginator->lastPage() }}</span>
        </div>

        <ul class="flex list-reset">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="mr-2 disabled opacity-50" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="border border-solid border-white px-3 py-2 cursor-not-allowed">Previous</span>
                </li>
            @else
                <li class="mr-2">
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="border border-solid border-white px-3 py-2 hover:bg-blue-700">Previous</a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="mr-2">
                    <a id="nextPage" href="{{ $paginator->nextPageUrl() }}" rel="next" class="border border-solid border-white px-3 py-2 hover:bg-blue-700">Next</a>
                </li>
            @else
                <li class="mr-2 disabled opacity-50" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="border border-solid border-white px-3 py-2 cursor-not-allowed">Next</span>
                </li>
            @endif
        </ul>
    </div>
@endif
