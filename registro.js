// Función para validar el formato del correo electrónico
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function registrar() {
    const nombre = document.getElementById('nombreRegistro').value; // Obtener el nombre
    const hotel = document.getElementById('hotelRegistro').value; // Obtener el hotel
    const email = document.getElementById('emailRegistro').value; // Obtener el email
    const password = document.getElementById('passwordRegistro').value; // Obtener la contraseña
    const rol = document.getElementById('rolRegistro').value; // Obtener el rol // Obtener el rol seleccionado

    // Agrega estos logs para ver qué se está capturando
    console.log('Nombre:', nombre);
    console.log('Hotel:', hotel);
    console.log('Email:', email);
    console.log('Password:', password);
    console.log('Rol:', rol);

    // Validación de campos
    if (nombre === '' || hotel === '' || email === '' || password === '' || !rol) {
        alert('Por favor, completa todos los campos');
        return;
    }

    // Validación del formato del correo
    if (!validateEmail(email)) {
        alert('Por favor, introduce un correo electrónico válido');
        return;
    }

    // Crear el objeto con los datos del formulario
    const data = { 
        nombre: nombre, 
        hotel: hotel, 
        email: email, 
        password: password, 
        rol: rol  // Incluir el rol
    };

    // Enviar los datos al servidor usando fetch
    fetch('procesar_registro.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.text())
    .then(responseText => {
        console.log('Respuesta del servidor:', responseText);

        // Intentar parsear la respuesta como JSON si es posible
        try {
            const parsedResponse = JSON.parse(responseText);
            return parsedResponse;
        } catch (error) {
            console.error('Error al parsear el JSON:', error);
            throw new Error('La respuesta del servidor no es un JSON válido.');
        }
    })
    .then(data => {
        if (data.success) {
            alert('Registro exitoso, ahora puedes iniciar sesión');
            document.getElementById('nombreRegistro').value = ''; // Limpiar el nombre
            document.getElementById('hotelRegistro').value = ''; // Limpiar el hotel
            document.getElementById('emailRegistro').value = ''; // Limpiar el email
            document.getElementById('passwordRegistro').value = ''; // Limpiar la contraseña
            document.getElementById('rolRegistro').value = '';
            window.location.href = 'index.php';  // Redirigir a la página de login
        } else {
            alert('Error: ' + data.message);  // Mostrar el mensaje de error del servidor
        }
    })
    .catch(error => {
        console.error('Error en el registro:', error);
        alert('Ocurrió un error durante el registro. Detalles: ' + error.message);
    });
}
