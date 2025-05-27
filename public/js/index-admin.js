document.addEventListener('DOMContentLoaded', function() {
    // Mejorar la visibilidad de las celdas al pasar el ratón
    const rows = document.querySelectorAll('.crud-table tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f0f7ff';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});