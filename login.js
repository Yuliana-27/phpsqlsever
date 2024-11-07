

function loguear() {
    const email = document.getElementById("email").value;
    const pass = document.getElementById("pass").value;

    if (email === '' || pass === '') {
        alert('Por favor, completa todos los campos');
        return;
    }

    const data = { email: email, password: pass };

    fetch('procesar_login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'PanelQr.php';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error en el inicio de sesión:', error);
        alert('Ocurrió un error durante el inicio de sesión. Detalles: ' + error.message);
    });
}
