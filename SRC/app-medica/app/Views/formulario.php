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
    
</style>

<div class="formulario">
    <h2>Carga de Informe</h2>
    <form class="form" enctype="multipart/form-data">
        <div class="datos1">
            <div>
                <label>Fecha</label>
                <input type="date" name="fecha">
            </div>
            <div>
                <label>Tipo de estudio</label>
                <select name="tipo_estudio">
                    <option value="VIDEOESOFAGASTRODUODENOSCOPIA">VIDEOESOFAGASTRODUODENOSCOPIA</option>
                    <option value="VIDEOCOLONOSCOPIA">VIDEOCOLONOSCOPIA</option>
                </select>
            </div>
        </div>

        <h3>Datos del paciente</h3>

        <div class="datos1">
            <div>
                <label>Nombre y apellido</label>
                <input type="text" name="nombre_apellido">
            </div>
            <div>
                <label>Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Edad</label>
                <input type="number" name="edad" id="edad" readonly>
            </div>
            <div>
                <label>Número de documento</label>
                <input type="text" name="documento">
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Tipo de cobertura</label>
                <input type="text" name="cobertura">
            </div>
            <div>
                <label>Número de afiliado</label>
                <input type="text" name="afiliado">
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>Mail</label>
                <input type="email" name="mail">
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
    <div>
        <label><input type="checkbox" id="vedaCheckbox">Es VEDA?</label>
    </div>
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
                <label>¿Se efectuó terapéutica?</label>
                <select name="terapeutico">
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>
            <div>
                <label>¿Cuál?</label>
                <select name="cual">
                    <option value="NINGUNO">NINGUNO</option>
                    <option value="POLIPECTOMIA">POLIPECTOMIA</option>
                    <option value="MUCOSECTOMIA">MUCOSECTOMIA</option>
                    <option value="DILATACION">DILATACION</option>
                </select>
            </div>
        </div>

        <div class="datos1">
            <div>
                <label>¿Se efectuó biopsia?</label>
                <select name="biopsia" id="">
                    <option value="SI">SI</option>
                    <option value="NO">NO</option>
                </select>
            </div>
            <div>
                <label>Cantidad de frascos</label>
                <input type="number" name="frascos">
            </div>
        </div>

        <label>Subir fotos</label>
        <input type="file" name="foto[]" accept="image/*" multiple>

        <button type="button" id="btnEnviar">Enviar</button>
    </form>
</div>
<script>
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

    document.getElementById('vedaCheckbox').addEventListener('change', function () {
        const vedaInputs = document.getElementById('vedaInputs');
        if (this.checked) {
            vedaInputs.style.display = 'block';
        } else {
            vedaInputs.style.display = 'none';
        }
    });

 
</script>
</body>
</html>
