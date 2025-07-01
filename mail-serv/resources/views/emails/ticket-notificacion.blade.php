<!DOCTYPE html>
<html>
<head>
    {{-- HEADER NO VISIBLE EN EL CORREO --}}
    {{-- <title>{{ $subject }}</title>
    <title>Confirmación de Orden de Trabajo Generada</title> --}}
</head>
<body>
    <h1>{{ $contentBody }}</h1>
    <h2>Orden de Trabajo Generada</h2>

    <li> From:  {{ $fromAddress }} </li>
    <li> To: {{ $toAddress }} </li>

    <h3>Asignada a:{{ $ticket['tecnico_user_name'] }}!</h3>

    <h3> Detalles de la OT: </h3>

    <ul> 
        
        <li> <strong>Cliente:</strong>   {{ $ticket['customer_name_selected'] }} </li>
        <li> <strong>Sitio:</strong>   {{ $ticket['selected_site_name'] }} </li>
        <li> <strong>Descripcion:</strong>   {{ $ticket['description'] }} </li>
        {{-- <li> Fecha de Creación: {{ $ticket['created_at'] }} </li> --}}
        <li> <strong>Prioridad:</strong>   {{ $ticket['priority'] }} </li>
        <li> <strong>Tipo de Trabajo:</strong>   {{ $ticket['worktype'] }} </li>
        <li> <strong>Tipo de Alarma:</strong>   {{ $ticket['alarmtype'] }} </li>
        <li> <strong>Estado:</strong>   {{ $ticket['status'] }} </li>
        {{-- <li> Descripcion: {{ $ticket['description'] }} </li> --}}
        <li> <strong>Supervisor:</strong>   {{ $ticket['supervisor_user_name'] }} </li>
        <li> <strong>Tecnico:</strong>   {{ $ticket['tecnico_user_name'] }} </li>
        <li> <strong>Creado por:</strong>   {{ $ticket['created_by_user_name'] }} </li>

    </ul>

    <p>Gracias por su atencion a la misma</p>
</body>
</html>
