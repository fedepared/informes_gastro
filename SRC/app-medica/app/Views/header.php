<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        * {
            font-family: 'DM Sans', sans-serif;;
        }
        .header{
            background-color:  #007bbd;
            padding: 5px;
            margin: -8px;
            color: #ffffff;
            text-decoration: none;
        }
        header .container-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ul-menu {
            list-style: none;
            display: flex;
            gap: 18px;
            margin-right: 22px;
            font-size: 18px;
        }
        .nombre {
            margin-left: 13px;
            font-size: 25px;
        }
        .menu {
            text-decoration: none;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container-header">
            <h1 class="nombre">Diana Estrin</h1>
            <nav class="nav">
                <ul class="ul-menu">
                    <li><a  class="menu" href="formulario">Carga de Informes</a></li>
                    <li ><a class="menu" href="reportes">Reportes</a></li>
                    <li ><a class="menu" href="coberturas">Coberturas</a></li>
                    <li ><a class="menu" href="login">Salir</a></li>
                </ul>
            </nav>
        </div>
    </header>
