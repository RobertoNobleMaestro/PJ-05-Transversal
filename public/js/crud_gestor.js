// Función sobre la vista del dashboard
document.addEventListener('DOMContentLoaded', function() {
    // Menú responsive
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-visible');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('sidebar-visible');
            overlay.classList.remove('active');
        });
    }
    
    // Filtro por rol
    document.querySelector('.filter-control').addEventListener('change', function() {
        const role = this.value.toLowerCase();
        const rows = document.querySelectorAll('.crud-table tbody tr');
        
        rows.forEach(row => {
            const roleCell = row.querySelector('td:nth-child(6)');
            if (roleCell) {
                const rowRole = roleCell.textContent.toLowerCase();
                if(role === '' || rowRole.includes(role)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
    
    // Buscador
    document.querySelector('.search-input').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.crud-table tbody tr');
        
        rows.forEach(row => {
            const nameCell = row.querySelector('td:nth-child(2)');
            const emailCell = row.querySelector('td:nth-child(5)');
            
            if (nameCell && emailCell) {
                const name = nameCell.textContent.toLowerCase();
                const email = emailCell.textContent.toLowerCase();
                
                if(name.includes(searchTerm) || email.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
    
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