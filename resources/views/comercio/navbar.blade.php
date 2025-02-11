<!-- resources/views/comercio/navbar.blade.php -->

<nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('tenant.comercios.records') }}">
            <img src="{{ $logo_url }}" alt="Logo" class="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle custom-nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categorías
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        @foreach($tags as $tag)
                            <li><a class="dropdown-item" href="{{ route('comercio.filtrar', $tag->id) }}">{{ $tag->name }}</a></li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link custom-nav-link active" aria-current="page" href="{{ route('tenant.comercios.records') }}">Inicio</a>
                </li>

            </ul>
<form class="navbar-search d-flex">
    <input id="search" type="search" class="form-control me-2" placeholder="Buscar...">
    <button type="submit" class="btn btn-outline-light">
        <i class="bi bi-search"></i>
    </button>
    <div id="searchResults"></div>
</form>
        </div>
    </div>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(document).ready(function() {
    let typingTimer;
    let doneTypingInterval = 300; // 300 ms de intervalo

    $('#search').on('keyup', function() {
        clearTimeout(typingTimer);
        var query = $(this).val();
        if (query.length > 0) {
            typingTimer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('comercio.buscar') }}",
                    type: "GET",
                    data: {'query': query},
                    success: function(data) {
                        $('#searchResults').empty();
                        if (data.length > 0) {
                            data.forEach(function(item) {
                                var itemHtml = `<div>
                                    <img src="${item.image_url}" alt="${item.description}">
                                    <div class="item-details">
                                        <a href="{{ url('producto') }}/${item.id}" class="text-decoration-none text-dark">${item.description}</a>
                                    </div>
                                    <div class="item-price">S/${item.sale_unit_price}</div>
                                </div>`;
                                $('#searchResults').append(itemHtml);
                            });
                            $('#searchResults').addClass('show');
                        } else {
                            $('#searchResults').append('<div>No se encontraron resultados</div>');
                            $('#searchResults').addClass('show');
                        }
                    }
                });
            }, doneTypingInterval);
        } else {
            $('#searchResults').empty();
            $('#searchResults').removeClass('show');
        }
    });

    // Para ocultar los resultados cuando se hace clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.navbar-search').length) {
            $('#searchResults').removeClass('show');
        }
    });
});
</script>


