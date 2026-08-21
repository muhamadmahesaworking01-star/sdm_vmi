@if ($paginator->hasPages())
    <nav class="mt-3" aria-label="Navigasi halaman">
        <ul class="pagination pagination-sm justify-content-end mb-0">
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link" href="{{ $paginator->previousPageUrl() ?: '#' }}" aria-label="Sebelumnya">Sebelumnya</a>
            </li>
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                </li>
            @endforeach
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link" href="{{ $paginator->nextPageUrl() ?: '#' }}" aria-label="Berikutnya">Berikutnya</a>
            </li>
        </ul>
    </nav>
@endif
