<?php include('header.php'); ?>
<style>
    .formulario {
        padding: 20px;
    width: 600px;
    margin: 20px auto;
    background-color: #ffffff;
    border: 2px solid #e1e1e1;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #007bbd;
        margin-bottom: 20px;
    }

    h3 {
        text-align: center;
        color: #007bbd;
        margin-bottom: 10px;
    }

    .form label {
        display: block;
        font-weight: bold;
        margin: 10px 0 5px;
    }

    /* Estilo para los inputs */
    .form input[type="text"],
    .form input[type="number"],
    .form input[type="email"],
    .form input[type="date"] {
        width: 241px;
    padding: 10px;
    margin-bottom: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
    }

    .form input[type="text"]:last-child,
    .form input[type="number"]:last-child,
    .form input[type="email"]:last-child,
    .form input[type="date"]:last-child {
        margin-right: 0;
    }

    /* Para los campos uno al lado del otro */
    .datos1 {
        display: flex;
        justify-content:  space-between;
       
    }

    /* Estilo para el área de texto */
    .form textarea {
        width: 96%;
        padding: 10px;
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        height: 150px;
    }

    .form input[type="file"] {
        width: 96%;
        padding: 10px;
        margin-bottom: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .form button {
        width: 100%;
        padding: 12px;
        background-color: #007bbd;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }

    .form button:hover {
        background-color: #005f8c;
    }
    select {
    height: 45px;
    border-radius: 4px;
    width: 260px;
    border-color: #c6bcbc;
    }
    .asterisco {
        color: #007bbd;
    }
    button:disabled {
            background-color: #009fdf61;
            cursor: not-allowed;
        }
        .alert {
            padding: 10px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            opacity: 1;
            transition: opacity 0.5s ease, height 0.5s ease, padding 0.5s ease;
        }

        

        button:disabled {
            background-color: #009fdf61;
            cursor: not-allowed;
        }

  
    .alert.success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
    }

    .alert.hidden {
        display: none;
    }
    #alert-message {
    position: fixed;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    width: 100vh;
    max-width: 90%;
    text-align: center;
}

.mensaje-error {
    color: red;
    font-size: 0.9em;
    display: flex

}
</style>


<div id="alert-message" class="alert hidden"></div>
<div class="formulario">
   <label>(<span class="asterisco">*</span>) Datos obligatorios</label> 
    <h2>Carga de Informe</h2>
    <form id="formInforme" class="form" enctype="multipart/form-data">

        <div class="datos1">
            <div>
                <label>Fecha <span class="asterisco">*</span></label>
                <input type="date" name="fecha" class="required-field">
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
            <div>
                <label>Tipo de estudio <span class="asterisco">*</span></label>
                <select name="tipo_informe" class="required-field">
                    <option value="VEDA">VIDEOESOFAGASTRODUODENOSCOPIA</option>
                    <option value="VCC">VIDEOCOLONOSCOPIA</option>
                </select>
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
        </div>

        <h3>Datos del paciente</h3>

        <div class="datos1">
            <div>
                <label>Nombre y apellido <span class="asterisco">*</span></label>
                <input type="text" name="nombre_paciente" class="required-field">
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
            <div>
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">
            </div>
        </div>

        <div class="datos1">
            <div >
                <label>Edad</label>
                <input type="number" name="edad" id="edad" readonly disabled="true">
            </div>
            <div>
                <label>Número de documento <span class="asterisco">*</span></label>
                <input type="text" name="dni_paciente" class="required-field">
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Tipo de cobertura <span class="asterisco">*</span></label>
                <select id="cobertura" name="id_cobertura" class="required-field">
                    <option value="">Cargando...</option>
                </select>
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
            <div>
                <label>Número de afiliado</label>
                <input type="text" name="afiliado" id="afiliado" disabled>
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Mail <span class="asterisco">*</span></label>
                <input type="email" name="mail_paciente" class="required-field">
                <span id="emailError" class="mensaje-error">El mail no es válido</span>
                <span class="mensaje-error">Este campo es obligatorio</span>
            </div>
            <div>
                <label>Médico que envía el estudio</label>
                <input type="text" name="medico">
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Motivo del estudio</label>
                <input type="text" name="motivo">
            </div>
        </div>

        <h3>Informe del estudio</h3>
        <label>Informe</label>
        <textarea name="informe"></textarea><br>
        <div class="datos1">
    
</div>

<!-- Inputs adicionales ocultos inicialmente -->
<div id="vedaInputs" style="display: none;">
    <div class="datos1">
        <div>
            <label>Estómago</label>
            <input type="text" name="estomago">
        </div>
        <div>
            <label>Duodeno</label>
            <input type="text" name="duodeno">
        </div>
    </div>
    <div class="datos1">
        <div style="width: 87%;">
            <label>Esófago</label>
            <input type="text" name="esofago" style="width: 46%;">
        </div>
    </div>
</div>


        <label>Conclusión</label>
        <textarea name="conclusion"></textarea><br>

        <div class="datos1">
            <div>
                <label>¿Se efectuó terapéutica? </label>
                <select name="terapeutico">
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>
            <div>
                <label>¿Cuál?</label>
                <select name="cual" >
                    <option value="POLIPECTOMIA">POLIPECTOMIA</option>
                    <option value="MUCOSECTOMIA">MUCOSECTOMIA</option>
                    <option value="DILATACION">DILATACION</option>
                </select>
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>¿Se efectuó biopsia? </label>
                <select name="biopsia" >
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>
            <div>
                <label>Cantidad de frascos</label>
                <input type="number" name="frascos">
            </div>
        </div>

        <label>Subir fotos <span class="asterisco">*</span></label>
        <input type="file" name="archivo[]" accept="image/*" multiple class="required-field">
        <span class="mensaje-error">Este campo es obligatorio</span>

        <button type="submit"  id="btnEnviar" disabled="true" >Enviar</button>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('formInforme');
    const requiredFields = form.querySelectorAll('.required-field');
    const btnEnviar = document.getElementById('btnEnviar');

    // Validación al salir del campo (blur)
    requiredFields.forEach(field => {
        const errorSpan = field.parentElement.querySelector('.mensaje-error');

        // Ocultar el mensaje al inicio
        errorSpan.style.display = 'none';

        // Mostrar error si está vacío al salir del campo
        field.addEventListener('blur', () => {
            if (!field.value.trim()) {
                errorSpan.style.display = 'flex';
            } else {
                errorSpan.style.display = 'none';
            }
            validarFormulario(); // validar después de tocar el input
        });

        // Ocultar error al escribir
        field.addEventListener('input', () => {
            if (field.value.trim()) {
                errorSpan.style.display = 'none';
            }
            validarFormulario(); // revalidar al escribir
        });
    });

    // Validación del email aparte
    const emailField = form.querySelector('input[name="mail_paciente"]');
    const emailError = document.getElementById('emailError');
    emailError.style.display = 'none';

    emailField.addEventListener('blur', () => {
        const emailValue = emailField.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (emailValue && !emailRegex.test(emailValue)) {
            emailError.style.display = 'flex';
        } else {
            emailError.style.display = 'none';
        }
        validarFormulario();
    });

    emailField.addEventListener('input', () => {
        emailError.style.display = 'none';
        validarFormulario();
    });

    // Habilita o deshabilita el botón según la validez
    function validarFormulario() {
        let valid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                valid = false;
            }
        });

        if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
            valid = false;
        }

        btnEnviar.disabled = !valid;
    }

    form.addEventListener('submit', function(event) {
    event.preventDefault(); // Prevenir el envío tradicional
    
    const formData = new FormData(form);
    const tipoEstudio = form.querySelector('select[name="tipo_informe"]').value;

    // Si el estudio es VCC, eliminar los campos específicos
    if (tipoEstudio === 'VCC') {
        formData.delete('estomago');
        formData.delete('duodeno');
        formData.delete('esofago');
    }

    const btnEnviar = document.getElementById('btnEnviar');
    btnEnviar.disabled = true;
    const originalText = btnEnviar.innerHTML;
    btnEnviar.innerHTML = 'Enviando...';

    fetch('<?= site_url('/informe/alta'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        let contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new Error("La respuesta no es JSON. Verifica tu backend.");
        }

        const data = await response.json();

        if (!response.ok) {
            console.error("Respuesta con error del servidor:", data);
            mostrarAlerta('Error del servidor', 'error');
        } else if (data.status === 'success') {
            mostrarAlerta('Formulario creado y enviado correctamente', 'success');
            form.reset();
            document.getElementById('vedaInputs').style.display = 'none'; // Ocultar inputs por si se reinicia
        } else {
            mostrarAlerta('Error al crear y enviar el formulario', 'error');
        }
    })
    .catch(error => {
        console.error("Error en la solicitud:", error);
        mostrarAlerta('Error en la conexión con el servidor', 'error');
    })
    .finally(() => {
        btnEnviar.disabled = false;
        btnEnviar.innerHTML = originalText;
    });
});
    const coberturaSelect = document.getElementById('cobertura');
    const afiliadoInput = document.getElementById('afiliado');

    // Aquí podrías continuar con el resto de tu lógica para cargar opciones
    // Ejemplo: cargarCoberturas();
});

  document.addEventListener('DOMContentLoaded', function () {
    const coberturaSelect = document.getElementById('cobertura');
    const afiliadoInput = document.getElementById('afiliado');

    function verificarCobertura() {
        const coberturaTexto = coberturaSelect.options[coberturaSelect.selectedIndex]?.text?.trim().toUpperCase();
        const coberturaValor = coberturaSelect.value;

        if (coberturaValor && coberturaTexto !== 'SIN COBERTURA') {
            afiliadoInput.disabled = false;
        } else {
            afiliadoInput.value = '';
            afiliadoInput.disabled = true;
        }
    }

    // Ejecutar al cambiar la cobertura
    coberturaSelect.addEventListener('change', verificarCobertura);

    // Si las opciones se cargan dinámicamente
    const observer = new MutationObserver(verificarCobertura);
    observer.observe(coberturaSelect, { childList: true });
});


 // Validación de campos obligatorios
 function validarFormulario() {
        const campos = document.querySelectorAll('.required-field');
        let formularioValido = true;

        campos.forEach(campo => {
            // Para archivos, verificar que se haya seleccionado al menos uno
            if (campo.type === "file") {
                if (campo.files.length === 0) formularioValido = false;
            } else if (campo.tagName === "SELECT") {
                if (campo.value === '') formularioValido = false;
            } else {
                if (campo.value.trim() === '') formularioValido = false;
            }
            if (campo.type === "email") {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(campo.value.trim())) {
                formularioValido = false;
                campo.classList.add("input-invalido");
            } else {
                campo.classList.remove("input-invalido");
            }
        }
        });

        // Habilita o deshabilita el botón
        document.getElementById('btnEnviar').disabled = !formularioValido;
    }

    // Escuchar cambios en todos los campos obligatorios
    window.addEventListener('DOMContentLoaded', () => {
        const campos = document.querySelectorAll('.required-field');
        campos.forEach(campo => {
            campo.addEventListener('input', validarFormulario);
            campo.addEventListener('change', validarFormulario); // Para select y file
        });

        validarFormulario(); // Validar al cargar la página
    });
   // Escuchar el cambio en el select de tipo de estudio
   document.querySelector('select[name="tipo_informe"]').addEventListener('change', function() {
        var tipoEstudio = this.value; // Obtenemos el valor seleccionado

        // Verificamos si es VIDEOESOFAGASTRODUODENOSCOPIA o VIDEOCOLONOSCOPIA
        var vedaInputs = document.getElementById('vedaInputs');
        if (tipoEstudio === 'VEDA') {
            vedaInputs.style.display = 'block'; // Mostrar los inputs
        } else {
            vedaInputs.style.display = 'none'; // Ocultar los inputs
        }
    });

    // Inicializar el estado del formulario dependiendo del tipo de estudio ya seleccionado (si ya está predefinido)
    window.addEventListener('DOMContentLoaded', function() {
        var tipoEstudio = document.querySelector('select[name="tipo_informe"]').value;
        if (tipoEstudio === 'VEDA') {
            document.getElementById('vedaInputs').style.display = 'block';
        } else {
            document.getElementById('vedaInputs').style.display = 'none';
        }
    });




    document.getElementById('fecha_nacimiento').addEventListener('change', function() {
        var fechaNacimiento = new Date(this.value);
        var hoy = new Date();
        var edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        var mes = hoy.getMonth() - fechaNacimiento.getMonth();

        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNacimiento.getDate())) {
            edad--;
        }

        document.getElementById('edad').value = edad;
    });

  
// Función para cargar las coberturas 
function cargarCoberturas() {
        // Realiza la solicitud AJAX
        fetch('<?= site_url('coberturas'); ?>')
            .then(response => response.json())
            .then(data => {
                // Verifica los datos que recibe
                // Una vez obtenidos los datos, llena el select
                const selectCobertura = document.getElementById('cobertura');
                selectCobertura.innerHTML = ''; // Limpia el select

                // Agrega una opción por defecto
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Seleccione una cobertura';
                selectCobertura.appendChild(defaultOption);

                // Agrega las opciones de coberturas al select
                if (Array.isArray(data)) {
                    data.forEach(cobertura => {
                        const option = document.createElement('option');
                        option.value = cobertura.id_cobertura; // El valor de la opción es el ID de la cobertura
                        option.textContent = cobertura.nombre_cobertura; // El texto es el nombre de la cobertura
                        selectCobertura.appendChild(option);
                    });
                } else {
                    console.error("La respuesta no es un array:", data);
                }
            })
            .catch(error => {
                console.error('Error al cargar las coberturas:', error);
                const selectCobertura = document.getElementById('cobertura');
                selectCobertura.innerHTML = '<option value="">Error al cargar coberturas</option>';
            });
    }

    // Llama a la función para cargar las coberturas cuando la página esté lista
    window.addEventListener('DOMContentLoaded', cargarCoberturas);
 
    

  

    validarFormulario(); // Esto asegura que también se actualice el botón Enviar
    document.addEventListener('DOMContentLoaded', function () {
    const emailInput = document.querySelector('input[name="mail_paciente"]');
    const emailError = document.getElementById('emailError');

    emailInput.addEventListener('input', function () {
        const email = emailInput.value;
        const isValid = validarEmail(email);

        if (email.length > 0 && !isValid) {
            emailError.style.display = 'flex';
        } else {
            emailError.style.display = 'none';
        }
    });

    function validarEmail(email) {
        // Expresión regular simple para validar un email
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
});
function mostrarAlerta(mensaje, tipo = 'error') {
        const alertDiv = document.getElementById("alert-message");

        alertDiv.textContent = mensaje;
        alertDiv.className = `alert ${tipo}`; // Aplica clase 'alert success' o 'alert error'
        alertDiv.classList.remove("hidden");

        clearTimeout(window.alertTimeout);
        window.alertTimeout = setTimeout(() => {
            alertDiv.classList.add("hidden");
        }, 4000);
    }

</script>
