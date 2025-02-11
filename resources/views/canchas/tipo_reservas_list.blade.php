<!-- resources/views/canchas/tipo_reservas_list.blade.php -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Ubicación</th>
            <th>Capacidad</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tiposReservas as $tipo)
            <tr>
                <td>{{ $tipo->nombre }}</td>
                <td>{{ $tipo->ubicacion }}</td>
                <td>{{ $tipo->capacidad }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
