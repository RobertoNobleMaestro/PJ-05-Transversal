@if ($paginator->hasPages())
    <nav class="taller-pagination" aria-label="Paginación">
        <ul class="pagination justify-content-center">
            {{-- Botón Anterior --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="Anterior">
                    <span class="page-link taller-page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link taller-page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Anterior">&laquo;</a>
                </li>
            @endif

            {{-- Números de página SOLO en pantallas grandes --}}
            <span class="d-none d-md-flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link taller-page-link">{{ $element }}</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link taller-page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link taller-page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            </span>

            {{-- Botón Siguiente --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link taller-page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Siguiente">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="Siguiente">
                    <span class="page-link taller-page-link">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
    <style>
        .taller-pagination .pagination { margin-bottom: 0; }
        .taller-page-link {
            color: #9F17BD;
            border: 1px solid #9F17BD;
            background: #fff;
            font-weight: 600;
        }
        .taller-page-link:hover, .taller-pagination .active .taller-page-link {
            background: #9F17BD;
            color: #fff;
            border-color: #9F17BD;
        }
        .taller-pagination .active .taller-page-link {
            box-shadow: 0 2px 8px rgba(159,23,189,0.09);
        }
        .taller-pagination .page-item.disabled .taller-page-link {
            color: #bbb;
            background: #f8f8fa;
            border-color: #e0e0e0;
        }
        /* Ocultar los números de página en pantallas pequeñas */
        @media (max-width: 767.98px) {
            .taller-pagination .d-md-flex { display: none !important; }
        }
    </style>
@endif
