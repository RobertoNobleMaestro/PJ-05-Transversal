document.addEventListener('DOMContentLoaded', function() {
    const loginButton = document.getElementById('login');
    const form = loginButton.closest('form');

    loginButton.addEventListener('click', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {
            email: formData.get('email'),
            password: formData.get('pwd')  // Cambiamos pwd a password
        };

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = data.redirect;
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ha ocurrido un error al intentar iniciar sesión');
        });
    });
});
