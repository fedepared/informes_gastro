<?php include('header.php'); ?>

<style>
    /* Contenedor principal para centrar la tabla debajo del header */
    .content {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        margin-top: 20px; /* Espacio entre el header y la tabla */
    }

    .container {
        width: 222%;
        max-width: 1081px;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
        text-align: center;
        margin: auto; /* Centra horizontalmente */
    }

    h2 {
        color: #007bbd;
        margin-bottom: 43px;
    }

    form {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        gap: 10px;
        flex-direction: row;
        align-items: center;
    }

    /* Estilo para los inputs y botones */
    input[type="date"], input[type="text"], button {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    button {
        background-color: #007bbd;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #005f99;
    }

    /* Estilización de la tabla */
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    th {
        background-color: #007bbd;
        color: white;
        text-transform: uppercase;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    /* Estilo para la lupa */

    /* Estilización de la caja de búsqueda */
.search-container {
    position: relative;
    display: flex;
    align-items: center;
    width: 19%;
}

input[type="text"] {
    padding: 8px 30px 8px 10px; /* Ajuste para dar espacio al icono */
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
    width: 81%; /* Hace que el input ocupe todo el espacio disponible */
}

/* Estilo para el icono */
.search-icon {
    position: absolute;
    right: 10px; /* Posiciona el icono a la derecha */
    color: #007bbd;
    font-size: 20px; /* Tamaño adecuado del icono */
    cursor: pointer;
}
    /* Paginador */
    .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
}

.pagination button {
    background-color: #007bbd;
    color: white;
    border: none;
    padding: 10px 15px;
    margin: 0 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s;
}

.pagination button:hover {
    background-color: #005f8a;
}

.pagination span {
    font-size: 16px;
    font-weight: bold;
}
td button {
    background-color: #007bbd;
    border: none;
    color: white;
    padding: 6px 8px;
    margin-right: 5px;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

td button:hover {
    background-color: #005f99;
}

.material-icons {
    vertical-align: middle;
}
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltip-text {
    visibility: hidden;
    width: 160px;
    background-color: #333;
    color: #fff;
    text-align: center;
    padding: 8px;
    border-radius: 6px;
    font-size: 16px;
    position: absolute;
    z-index: 1;
    bottom: 125%; /* Muestra el tooltip arriba del botón */
    left: 50%;
    transform: translateX(-50%);
    opacity: 0;
    transition: opacity 0.3s;
}

.tooltip:hover .tooltip-text {
    visibility: visible;
    opacity: 1;
}
.pagination button:disabled {
    background-color: #007bbda8;
    cursor: not-allowed;
    opacity: 0.7;
}
/* Estilos para el fondo oscuro del modal */
#modal-editar {
    display: none; /* Oculto por defecto */
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5); /* Fondo semitransparente */
    z-index: 999;
    justify-content: center;
    align-items: center;
}

/* Estilo del contenido del modal */
#modal-editar .modal-content {
    background-color: white;
    padding: 30px;
    border-radius: 15px;
    width: 17%;
    max-width: 400px;
    box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
    text-align: left;
}

/* Encabezado del modal */
#modal-editar h3 {
    margin-top: 0;
    color: #007bbd;
    font-size: 20px;
    text-align: center;
}

/* Inputs del formulario */
#modal-editar input[type="text"] {
    width: 94%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 14px;
}

/* Botones del modal */
#modal-editar button {
    padding: 10px 15px;
    margin-right: 10px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.3s ease;
}

#modal-editar button[type="submit"] {
    background-color: #007bbd;
    color: white;
}

#modal-editar button[type="submit"]:hover {
    background-color: #005f8a;
}

#modal-editar button[type="button"] {
    background-color: #ff5a5a;
}

#modal-editar button[type="button"]:hover {
    background-color: #ff5a5ab5;
}
.modal-close {
    display: flex;
    width: 100%;
    flex-direction: column;
}
.close {
    text-align: end;
    color: #007bbd;
    font-size: x-large;
    cursor: pointer;
}
.alert {
        padding: 15px;
        margin-top: 20px;
        border-radius: 8px;
        text-align: center;
        font-weight: bold;
        transition: all 0.3s ease;
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
</style>

<div id="mensaje-alerta" class="alert hidden"></div>
<div class="content">
    <div class="container">
        <h2>Reportes</h2>

        <!-- Formulario para seleccionar el rango de fechas -->
        <form id="filterForm">
            <div class="search-container">
                <!-- <label for="nombre">Nombre:</label> -->
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese nombre">
                <span class="material-icons search-icon" id="search-icon" >search</span>
            </div>

            <div>
                <label for="start_date">Desde:</label>
                <input type="date" name="start_date" id="start_date">

                <label for="end_date">Hasta:</label>
                <input type="date" name="end_date" id="end_date">

                <button type="submit">Filtrar</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>DNI</th>
                    <th>Fecha</th>
                    <th>Mail</th>
                    <th>Tipo</th>
                    <th>Cobertura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="4">Cargando datos...</td></tr>
            </tbody>
        </table>
        <!-- Paginación -->
        <div class="pagination">
            <button id="prevPage"  disabled>Anterior</button>
            <span id="pageNumber">1</span>
            <button id="nextPage" disabled>Siguiente</button>
        </div>
    </div>
</div>
<!-- Modal para editar reporte -->
<div id="modal-editar">
    <div class="modal-content">
    <div class="modal-close">

<span class="close" onclick="cerrarModal()">&times;</span>
</div>
        <h3>Editar Reporte</h3>
        <form id="formEditarReporte" style="
    display: flex;
    flex-direction: column;
    align-items: stretch;
">
            <input type="hidden" id="edit-id">

            <label for="edit-nombre">Nombre:</label>
            <input type="text" id="edit-nombre" required>

            <label for="edit-mail">Correo:</label>
            <input type="text" id="edit-mail" required>

            <div style="display: flex; justify-content: flex-end;">
                <button type="button" onclick="cerrarModal()">Cancelar</button>
                <button type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>



<script>
document.addEventListener("DOMContentLoaded", function () {
    fetchInformes();

    // Eventos para la búsqueda por nombre
    document.getElementById("search-icon").addEventListener("click", function () {
        filtrarTabla();
    });

    document.getElementById("nombre").addEventListener("keyup", function () {
        filtrarTabla();
    });

    // Evento para el botón de filtrar por fecha
    document.getElementById("filterForm").addEventListener("submit", function (event) {
        event.preventDefault(); // Evita recargar la página
        filtrarTabla();
    });

    // Paginación
    document.getElementById("prevPage").addEventListener("click", function() {
        changePage(-1);
    });

    document.getElementById("nextPage").addEventListener("click", function() {
        changePage(1);
    });
});

let reportes = [];
let currentPage = 1;
let itemsPerPage = 5; // Cambia este valor para ajustar la cantidad de elementos por página

function fetchInformes() {
   

    fetch('<?= site_url('informes'); ?>', {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
       
        reportes = data;
        updatePagination(); // Inicializa la paginación al recibir los datos
    })
    .catch(error => console.error("Error en la solicitud:", error));
}

function updatePagination() {
    let totalPages = Math.ceil(reportes.length / itemsPerPage);
    document.getElementById("pageNumber").textContent = currentPage;

    // Deshabilita los botones cuando sea necesario
    document.getElementById("prevPage").disabled = currentPage === 1;
    document.getElementById("nextPage").disabled = currentPage === totalPages || totalPages === 0;

    mostrarPagina();
}

function mostrarPagina() {
    let tbody = document.querySelector("table tbody");
    tbody.innerHTML = ""; // Limpiar la tabla

    let start = (currentPage - 1) * itemsPerPage;
    let end = start + itemsPerPage;
    let datosPagina = reportes.slice(start, end);

    if (datosPagina.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5">No hay reportes disponibles.</td></tr>`;
        return;
    }

    datosPagina.forEach(reporte => {
        let fila = `
            <tr>
                <td>${reporte.nombre_paciente || ""}</td>
                <td>${reporte.dni_paciente || ""}</td>
                <td>${reporte.fecha === "0000-00-00" ? "" : reporte.fecha}</td>
                <td>${reporte.mail_paciente || ""}</td>
                <td>${reporte.tipo_informe || ""}</td>
                <td>${reporte.nombre_cobertura || ""}</td>
                 <td>
            <div class="tooltip">
                <button onclick="descargarReporte('${reporte.url_archivo}')">
                    <span class="material-icons">download</span>
                </button>
                <div class="tooltip-text">Descargar Reporte</div>
            </div>
            <div class="tooltip">
                <button onclick="reenviarReporte(${reporte.id_informe})">
                    <span class="material-icons">send</span>
                </button>
                <div class="tooltip-text">Reenviar Reporte</div>
            </div>
            <div class="tooltip">
                <button onclick='abrirModalEditar(${JSON.stringify(reporte)})'>
    <span class="material-icons">edit</span>
</button>
                <div class="tooltip-text">Editar Reporte</div>
            </div>
        </td>
            </tr>
        `;
        tbody.innerHTML += fila;
    });
}

// Función para cambiar de página
function changePage(direction) {
    let totalPages = Math.ceil(reportes.length / itemsPerPage);

    if ((direction === -1 && currentPage === 1) || (direction === 1 && currentPage === totalPages)) {
        return; // No avanzar si ya estamos en el límite
    }

    currentPage += direction;
    updatePagination();
}

// 🔹 Función para filtrar la tabla por nombre y rango de fecha
function filtrarTabla() {
    let filtroNombre = document.getElementById("nombre").value.toLowerCase();
    let fechaInicio = document.getElementById("start_date").value;
    let fechaFin = document.getElementById("end_date").value;

    let filas = document.querySelectorAll("table tbody tr");

    filas.forEach(fila => {
        let nombre = fila.cells[0].textContent.toLowerCase();
        let fecha = fila.cells[1].textContent;

        let cumpleFiltroNombre = nombre.includes(filtroNombre);
        let cumpleFiltroFecha = true;

        if (fechaInicio && fechaFin) {
            cumpleFiltroFecha = fecha >= fechaInicio && fecha <= fechaFin;
        } else if (fechaInicio) {
            cumpleFiltroFecha = fecha >= fechaInicio;
        } else if (fechaFin) {
            cumpleFiltroFecha = fecha <= fechaFin;
        }

        if (cumpleFiltroNombre && cumpleFiltroFecha) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }
    });
}

function descargarReporte(url) {
    const checkUrl = `<?= site_url('descargar-archivo?url='); ?>${encodeURIComponent(url)}`;

    // Paso 1: Verificar que la carpeta exista y tenga archivos
    fetch(checkUrl)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Mostrar mensaje verde y hacer la descarga con redirección clásica
                mostrarMensaje('success', data.message || 'Iniciando descarga...');
                setTimeout(() => {
                    window.location.href = `<?= site_url('descargar-archivo?url='); ?>${encodeURIComponent(url)}`;
                }, 1000);
            } else {
                // Mostrar mensaje de error
                mostrarMensaje('error', data.message || 'No se pudo procesar la descarga');
            }
        })
        .catch(err => {
            console.error(err);
            mostrarMensaje('error', 'Ocurrió un error al verificar la carpeta');
        });
}



function reenviarReporte(id) {
    fetch('<?= site_url('/informe/reenviar-informe/'); ?>' + id)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    mostrarMensaje("success", "Informe reenviado correctamente");
                } else {
                    mostrarMensaje("error", "No se pudo reenviar el informe");
                }
            })
            .catch(error => {
                mostrarMensaje("error", "Error al reenviar el informe");
                
            });
}


function abrirModalEditar(reporte) {
    document.getElementById('edit-id').value = reporte.id_informe;
    document.getElementById('edit-nombre').value = reporte.nombre_paciente || '';
    document.getElementById('edit-mail').value = reporte.mail_paciente || '';
    
 

    document.getElementById('modal-editar').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modal-editar').style.display = 'none';
}
document.getElementById("formEditarReporte").addEventListener("submit", function (e) {
    e.preventDefault();

    const id = document.getElementById("edit-id").value;
    const nombre = document.getElementById("edit-nombre").value;
    const mail = document.getElementById("edit-mail").value;

    const datos = {
        nombre_paciente: nombre,
        mail_paciente: mail,
    };

    fetch(`<?= site_url('informe/editar/'); ?>${id}`, {

        method: "PUT",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(datos),
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === "success") {
            // alert("Reporte actualizado correctamente");
            cerrarModal();
            fetchInformes(); // Vuelve a cargar los datos
            mostrarMensaje("success", "Reporte actualizado correctamente");
        } else {
            mostrarMensaje("error", "Error al actualizar: " + result.message);
        }
    })
    .catch(error => {
        mostrarMensaje("error", "Ocurrió un error en la solicitud.");
    });
});

function mostrarMensaje(tipo, texto) {
    const alerta = document.getElementById('mensaje-alerta');
    alerta.textContent = texto;
    alerta.className = 'alert ' + tipo; // "alert success" o "alert error"
    
    // Mostrar
    alerta.classList.remove('hidden');

    // Ocultar luego de unos segundos
    setTimeout(() => {
        alerta.classList.add('hidden');
    }, 4000);
}
</script>
