
<?php include('header.php'); ?> 

<style>
    body {
        background-color: #f8f9fa;
        font-family: Arial, sans-serif;
    }

    .container {
        width: 55%;
        margin: 39px auto;
    }

    .card {
        background: white;
        padding: 27px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: 2px solid #e1e1e1;
    }

    h2 {
        text-align: center;
        color: #007bbd;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 8px;
        overflow: hidden;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #007bbd;
        color: white;
    }

    .btn {
        padding: 8px 12px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        border-radius: 5px;
        font-size: 14px;
        transition: 0.3s;
    }

    .btn-add {
        background-color: #28a745;
        color: white;
        margin-bottom: 15px;
        display: inline-block;
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: bold;
    }

    
    .btn:hover {
        opacity: 0.8;
    }

    /* Estilo para el modal */
   /* Estilo para el modal */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4); /* Fondo semitransparente */
    padding-top: 60px;
    transition: all 0.4s ease-in-out;
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 400px; /* Tamaño común para todos los modales */
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.modal-header {
    font-size: 1.5em;
    margin-bottom: 18px;
    color: #007bbd;
    text-align: center;
}

.modal-footer {
    text-align: center;
    margin-top: 15px;
}

.modal-button {
    padding: 10px 15px;
    background-color: #28a745;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    border: none;
    font-weight: bold;
    margin-right: 10px;
}

.modal-button.cancel {
    background-color: #dc3545;
}

.modal-button:hover {
    opacity: 0.8;
}

/* Estilo para el botón de cerrar */
.close {
    text-align: end;
    color: #007bbd;
    font-size: x-large;
    cursor: pointer;
}

/* Estilo de los inputs dentro del modal */
input[type="text"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

input[type="text"]:focus {
    border: 1px solid #007bbd;
    box-shadow: 0 0 5px rgba(0, 123, 189, 0.5);
    outline: none;
}

label {
    font-size: 16px;
    color: #333;
    margin-bottom: 5px;
}


    /* Estilo para los botones en la tabla */
    .btn-edit, .btn-delete {
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 14px;
        cursor: pointer;
    }

  
    /* Estilo general para los inputs */
input[type="text"] {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    transition: border 0.3s ease, box-shadow 0.3s ease;
}

/* Estilo cuando el input está en foco */
input[type="text"]:focus {
    border: 1px solid #007bbd;
    box-shadow: 0 0 5px rgba(0, 123, 189, 0.5);
    outline: none;
}

/* Estilo de las etiquetas */
label {
    font-size: 16px;
    color: #333;
    margin-bottom: 5px;
}

/* Estilo para los inputs dentro del modal */
.modal-content input[type="text"] {
    margin-top: 8px;
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

.modal-close {
    display: flex;
    width: 100%;
    flex-direction: column;
}


</style>

<div class="container">
    <div class="card">
        <h2>Lista de Coberturas</h2>
        <button class="btn btn-add" onclick="showModal('addModal')">Agregar Cobertura</button>
        
        <!-- Tabla de coberturas -->
        <table>
            <thead>
                <tr>
                    <th>Nombre Cobertura</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tablaCoberturas">
                <tr>
                    <td colspan="2">Cargando coberturas...</td>
                    
                </tr>
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="pagination">
            <button id="prevPage" onclick="changePage(-1)" disabled>Anterior</button>
            <span id="pageNumber">1</span>
            <button id="nextPage" onclick="changePage(1)" disabled>Siguiente</button>
        </div>
    </div>
</div>
<!-- Modal para agregar cobertura -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-close">

            <span class="close" onclick="closeModal('addModal')">&times;</span>
        </div>
        <div class="modal-header">
            <h3>Agregar Cobertura</h3>
        </div>
        <form action="cobertura/alta" method="post">
            <label for="nombre_cobertura">Nombre Cobertura:</label>
            <input type="text" id="nombre_cobertura" name="nombre_cobertura" required><br><br>
            <div class="modal-footer">
                <button type="submit" class="modal-button">Guardar</button>
                <button type="button" class="modal-button cancel" onclick="closeModal('addModal')">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para editar cobertura -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-close">

            <span class="close" onclick="closeModal('editModal')">&times;</span>
        </div>
        <div class="modal-header">
            <h3>Editar Cobertura</h3>
        </div>
        <form id="editForm" method="post" action="">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="id_cobertura" id="edit_id">
    <label for="edit-nombre_cobertura">Nombre Cobertura:</label>
    <input type="text" name="nombre_cobertura" id="edit_nombre" required>
    <div class="modal-footer">
        <button type="submit" class="modal-button">Guardar</button>
        <button type="button" class="modal-button cancel" onclick="closeModal('editModal')">Cancelar</button>
    </div>
</form>
    </div>
</div>

<!-- Modal para eliminar cobertura -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <div class="modal-close">

            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
        </div>
        <div class="modal-header">
            <h3>Eliminar Cobertura</h3>
        </div>
        <form id="deleteForm" method="post" action="<?= site_url('cobertura/borrar') ?>">
        <input type="hidden" name="_method" value="DELETE">
        <input type="hidden" name="id_cobertura" id="delete_id">
    <p style="padding-bottom: 27px;">¿Estás seguro de eliminar esta cobertura?</p>
    <div class="modal-footer">
        <button type="submit" class="modal-button">Eliminar</button>
        <button type="button" class="modal-button cancel" onclick="closeModal('deleteModal')">Cancelar</button>
    </div>
</form>
    </div>
</div>

<script>
let coberturas = []; // Variable global para almacenar las coberturas
    let currentPage = 1;
    const itemsPerPage = 5; // Cantidad de coberturas por página

    window.onload = function() {
        cargarCoberturas();
    };

    function cargarCoberturas() {
        fetch('<?= site_url('coberturas'); ?>')
        .then(response => response.json())
        .then(data => {
            console.log("Datos recibidos:", data);  
            coberturas = data; // Guarda los datos en la variable global
            renderTable(); // Renderiza la tabla
        })
        .catch(error => {
            console.error('Error al cargar las coberturas:', error);
        });
    }

    function renderTable() {
        const tablaCoberturas = document.getElementById('tablaCoberturas');
        tablaCoberturas.innerHTML = ''; // Limpia la tabla antes de agregar filas

        if (coberturas.length === 0) {
            tablaCoberturas.innerHTML = '<tr><td colspan="2">No hay coberturas disponibles.</td></tr>';
            return;
        }

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const paginatedItems = coberturas.slice(startIndex, endIndex); // Obtiene los elementos de la página actual

        paginatedItems.forEach(cobertura => {
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td>${cobertura.nombre_cobertura}</td>
                <td>
                    <button style="width: 63px;height: 30px;border: none;border-radius: 6px;color: #000000; background-color: #ffc107;" 
                        onclick="showModal('editModal', ${cobertura.id_cobertura}, '${cobertura.nombre_cobertura}')">Editar</button>
                    <button style="height: 30px;border: none; border-radius: 6px;width: 71px; color: #fff; background-color: #dc3545;"
                        onclick="showModalDelete(${cobertura.id_cobertura})">Eliminar</button>
                </td>
            `;
            tablaCoberturas.appendChild(fila);
        });

        actualizarPaginacion();
    }

    function actualizarPaginacion() {
        document.getElementById('pageNumber').textContent = currentPage;
        document.getElementById('prevPage').disabled = currentPage === 1;
        document.getElementById('nextPage').disabled = currentPage * itemsPerPage >= coberturas.length;
    }

    function changePage(offset) {
        currentPage += offset;
        renderTable();
    }


  






    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
    }
    function showModal(modalId, id = null, nombre = '') {
    let modal = document.getElementById(modalId);
    if (modalId === 'editModal') {
        // Asigna el ID y nombre a los campos del formulario
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nombre').value = nombre;

        // Asigna la URL correcta para el formulario de edición
        document.getElementById('editForm').action = '/app-medica/public/index.php/cobertura/editar/' + id;
    }
    modal.style.display = "block";

    }
    function showModalDelete(id) {
    
    document.getElementById('deleteForm').action = '/app-medica/public/cobertura/borrar/' + id;
    document.getElementById('deleteModal').style.display = "block";
}
</script>

