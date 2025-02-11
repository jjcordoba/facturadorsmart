<!-- resources/views/comercio/footer.blade.php -->

<footer class="footer mt-auto py-3 custom-footer">
    <div class="container footer-container">
        <a class="footer-logo" href="{{ route('tenant.comercios.records') }}">
            <img src="{{ $logo_url }}" alt="Logo" class="logo">
</a>
        <div class="footer-links">
            <a href="{{ route('tenant.comercios.records') }}" class="footer-link">Inicio</a>
            <!-- <a href="#" class="footer-link">Contacto</a> -->
        </div>
<div class="footer-contact">
    <strong>Contacto:</strong>
    <ul class="contact-details">
        <li>{{ $contact_name }}</li>
        <li>Correo: {{ $contact_email }}</li>
        <li>Celular: {{ $contact_phone }}</li>
        <li>Dirección: {{ $contact_address }}</li>
    </ul>
</div>



<div class="footer-social">
    <a href="{{ $link_facebook }}" class="social-icon facebook" target="_blank"><i class="bi bi-facebook"></i></a>
    <a href="{{ $link_twitter }}" class="social-icon twitter" target="_blank"><i class="bi bi-twitter"></i></a>
    <a href="{{ $link_instagram }}" class="social-icon instagram" target="_blank"><i class="bi bi-instagram"></i></a>
</div>
    </div>
    <div class="text-center mt-3">
        <span class="text-muted">© 2024 Facturaperu. Todos los derechos reservados.</span>
    </div>
</footer>
