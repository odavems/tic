<!DOCTYPE html>
<html>
<head>
    {{-- ESTA PARTE NO SE IMPRIME, es header HTML --}}

    {{-- <title>{{ $subject }}</title>
    <title>Titulo Confirmación de Orden de Trabajo Generada</title> --}}

</head>
<body>
    {{-- INICIO DEL MAIL --}}

    <p>Inicio del Body en el view</p>
    <h1>Body h1 var contentBody: {{ $contentBody }}</h1>

    <h1>Body h1 txt: Orden de Trabajo Generada</h1>

    <li>Body var fromAddress From:  {{ $fromAddress }} </li>
    <li>Body var toAddress To: {{ $toAddress }} </li>

    <p>var assigned_to_uuid Orden de Trabajo asignada a:{{ $ticket['assigned_to_uuid'] }}!</p>

    <p> Detalles de la OT: </p>

    {{-- TEST BASICO FUNCIONA SIN LA LINEA $ticket ya que es ARRAY --}}

    <ul> 
        <li> Cliente:  {{ $ticket['customer_id'] }} </li>
        <li> From:  {{ $fromAddress }} </li>
        <li> To: {{ $toAddress }} </li>
        {{-- <li> CC: {{ $toAddress }} </li> --}}
    </ul>



    @foreach($ticket as $singleticket)
        <ul> <p>Inicio del foreach array si es que es ARRAY</p>
            @if(is_array($singleticket))
                @if(isset($singleticket['ticket_id']))
                    <li> ID de la Orden de Trabajo:  {{ $singleticket['ticket_id'] }}</li>
                    //tabla tickets
                @endif
                @if(isset($singleticket['customer_id']))
                    <li> Cliente:  {{ $singleticket['customer_id'] }} </li>
                    //tabla tickets
                @endif
                @if(isset($singleticket['site_id']))
                    <li> SITIO: $ {{ $singleticket['site_id'] }} </li>
                    //tabla tickets
                @endif
            @else
                <li>(ELSE TODO) Imprime todo el array JSON -> {{ $singleticket }}</li>

            @endif
        </ul>
    @endforeach


    <h2>Detalle de la Orden de Trabajo asignada:</h2>

    {{-- <ul>
        @foreach ($tickets as $ticket)
            <li>
                <strong>Ticket Nro:</strong> {{ $item['ticket_id'] }}<br>
                <strong>Tipo de Trabajo:</strong> {{ $item['worktype'] }}<br>
                <strong>Tipo de Alarma:</strong> {{ $item['alarmtype'] }}<br>
                <strong>Estado:</strong> {{ $item['status'] }}<br>
                <strong>Prioridad:</strong> {{ $item['priority'] }}<br>
                <strong>Creado el:</strong> {{ $item['created_at'] }}<br>
                <strong>Supervisor Encargado:</strong> {{ $item['supervisor_uuid'] }}<br>
                <strong>Asignado a:</strong> {{ $item['assigned_to_uuid'] }}<br>
            </li>
        @endforeach
    </ul> --}}

    <p><strong>{{ $contentBody }}</p>
    <p><strong>Prioridad:</strong> {{ $ticket['priority'] }}</p>
    {{-- <p><strong>Estado:</strong> {{ ucfirst($ticket['status']) }}</p> --}}
    <p><strong>Estado:</strong> {{ $ticket['status'] }}</p>

    <p>Gracias por su atencion a la misma</p>
</body>
</html>
