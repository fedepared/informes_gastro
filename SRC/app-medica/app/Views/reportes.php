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
        margin-bottom: 15px;
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
    .search-icon {
        cursor: pointer;
        margin-left: -35px; /* Ajuste para que se superponga al input */
        color: #007bbd;
        font-size: 18px;
    }

    .search-container {
        position: relative;
    }
</style>

<div class="content">
    <div class="container">
        <h2>Reportes</h2>

        <!-- Formulario para seleccionar el rango de fechas -->
        <form method="GET">
            <div class="search-container">
                <label for="nombre">Nombre:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Ingrese nombre">
               
            </div>

            <div>
                <label for="start_date">Desde:</label>
                <input type="date" name="start_date" id="start_date" required>

                <label for="end_date">Hasta:</label>
                <input type="date" name="end_date" id="end_date" required>

                <button type="submit">Filtrar</button>
            </div>
        </form>

        <?php
        // Datos de reportes simulados
        $reportes = [
            ["fecha" => "2025-03-25", "nombre" => "Juan Pérez", "estudio" => "VEDA", "cobertura" => "OSDE"],
            ["fecha" => "2025-03-26", "nombre" => "María López", "estudio" => "VCC", "cobertura" => "OSDE"],
            ["fecha" => "2024-03-27", "nombre" => "Carlos Gómez", "estudio" => "VEDA", "cobertura" => "Swiss Medical"],
        ];

        // Verifica si se enviaron las fechas
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];

            // Filtrar los reportes por el rango de fechas
            $reportes_filtrados = array_filter($reportes, function ($reporte) use ($start_date, $end_date) {
                return $reporte['fecha'] >= $start_date && $reporte['fecha'] <= $end_date;
            });
        } else {
            $reportes_filtrados = $reportes; // Mostrar todos los reportes por defecto
        }

        
        ?>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Tipo de Estudio</th>
                    <th>Cobertura</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reportes_filtrados)) : ?>
                    <?php foreach ($reportes_filtrados as $reporte) : ?>
                        <tr>
                            <td><?= $reporte['fecha'] ?></td>
                            <td><?= $reporte['nombre'] ?></td>
                            <td><?= $reporte['estudio'] ?></td>
                            <td><?= $reporte['cobertura'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">No hay reportes en este rango de fechas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


