<!DOCTYPE html>
<html>
<head>
    
    
</head>
<body>
    <h1><?php echo e($contentBody); ?></h1>
    <h2>Orden de Trabajo Generada</h2>

    <li> From:  <?php echo e($fromAddress); ?> </li>
    <li> To: <?php echo e($toAddress); ?> </li>

    <h3>Asignada a:<?php echo e($ticket['tecnico_user_name']); ?>!</h3>

    <h3> Detalles de la OT: </h3>

    <ul> 
        
        <li> <strong>Cliente:</strong>   <?php echo e($ticket['customer_name_selected']); ?> </li>
        <li> <strong>Sitio:</strong>   <?php echo e($ticket['selected_site_name']); ?> </li>
        <li> <strong>Descripcion:</strong>   <?php echo e($ticket['description']); ?> </li>
        
        <li> <strong>Prioridad:</strong>   <?php echo e($ticket['priority']); ?> </li>
        <li> <strong>Tipo de Trabajo:</strong>   <?php echo e($ticket['worktype']); ?> </li>
        <li> <strong>Tipo de Alarma:</strong>   <?php echo e($ticket['alarmtype']); ?> </li>
        <li> <strong>Estado:</strong>   <?php echo e($ticket['status']); ?> </li>
        
        <li> <strong>Supervisor:</strong>   <?php echo e($ticket['supervisor_user_name']); ?> </li>
        <li> <strong>Tecnico:</strong>   <?php echo e($ticket['tecnico_user_name']); ?> </li>
        <li> <strong>Creado por:</strong>   <?php echo e($ticket['created_by_user_name']); ?> </li>

    </ul>

    <p>Gracias por su atencion a la misma</p>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/emails/ticket-notificacion.blade.php ENDPATH**/ ?>