@extends('layouts.admin')

@section('title', 'Panel de Administrador')

@section('content')
    <style>
        .admin-container {
            margin-top: 50px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .admin-container h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .admin-container p {
            font-size: 1.2rem;
            color: #6c757d;
        }
        .admin-container nav ul {
            list-style-type: none;
            padding: 0;
        }
        .admin-container nav ul li {
            display: inline;
            margin-right: 15px;
        }
        .admin-container nav ul li a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .admin-container nav ul li a:hover {
            text-decoration: underline;
        }
        .large-box {
            width: 50%;
            height: 70vh;
            margin: 50px auto;
            background-image: url('{{ asset('img/User.JPG') }}');
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease, opacity 0.3s ease;
            cursor: pointer;
            opacity: 1;
        }
        .large-box:hover {
            opacity: 0.7;
        }
        .large-box-text {
            font-size: 1.5rem;
            color: #495057;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .large-box:hover .large-box-text {
            opacity: 1;
        }
    </style>
    <div class="admin-container">
        <h1>Bienvenido, Administrador</h1>
        <p>Usuario: admin@carflow.com</p>
        <div class="large-box" onclick="window.location='{{ route('admin.users') }}'">
            <span class="large-box-text">CRUD de usuarios</span>
        </div>
        <nav>
            <ul>
                <!-- Enlace eliminado ya que el contenedor hace su función -->
            </ul>
        </nav>
    </div>
@endsection
