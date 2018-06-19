<?php

return [
    
    'rol' => [
        'direccion' => 2,
        'jefe_dpto' => 13,
        'tutor' => 17,
        'mantenimiento' => 7,
        'profesor' => 3,
        'alumno' => 5,
        'administrador' => 11,
        'todos' => 1,
        'conserge' => 23,
        'orientador' => 29,
        'practicas' => 31
    ],
    'lor' => [
        2 => 'direccion',
        13 => 'jefe_dpto',
        17 => 'tutor',
        7 => 'mantenimiento',
        3 => 'profesor',
        11 => 'administrador',
        23 => 'conserge',
        29 => 'orientador',
        5 => 'alumno',
        1 => 'todos',
        31 => 'practicas'
    ],
    'diaSemana' => [
        '1' => 'L',
        '2' => 'M',
        '3' => 'X',
        '4' => 'J',
        '5' => 'V',
        '6' => 'S',
        '7' => 'D'
    ],
    'icon' => [
        'delete' => 'fa-eraser',
        'edit' => 'fa-edit',
        'read' => 'fa-eyedropper',
        'authorize' => 'fa-smile-o',
        'unauthorize' => 'fa-frown-o',
        'process' => 'fa-smile-o',
        'noprocess' => 'fa-frown-o',
        'show' => 'fa-eye',
        'detalle' => 'fa-plus-square',
        'carnet' => 'fa-credit-card',
        'refuse' => 'fa-remove',
        'resolve' => 'fa-smile-o',
        'pdf' => 'fa-file-pdf-o',
        'autorizacion' => 'fa-file-pdf-o',
        'email' => 'fa-envelope',
        'init' => 'fa-envelope',
        'notification' => 'fa-bell',
        'active' => 'fa-check',
        'horario' => 'fa-table',
        'copy' => 'fa-copy',
        'muestra' => 'fa-image',
        'justify' => 'fa-thumbs-o-up',
        'baja' => 'fa-remove',
        'register' => 'fa-thumbs-o-up',
        'unregister' => 'fa-thumbs-o-down',
        'cancel' => 'fa-times',
        'document' => 'fa-file',
        'unpaid' => 'fa-money',
        'saveFile' => 'fa-send',
        'anexo' => 'fa-plus',
        'up' => 'fa-arrow-up',
        'down' => 'fa-arrow-down',
        'orden' => 'fa-calendar',
        'open' => 'fa-toggle-on',
    ],
    'completa' => 45,
    'precioKilometro' => 0.19,
    'reservaAforo' => 1.2,
    'codigoGuardia' => '3249454',
    'estadoMaterial' => ['??','OK','Reparandose','Baja'],
    'programaciones' => [ 'fichero' => '1' , 'mostrar' => '3' , 'enlace' => '0'],
    'procedenciaMaterial' => ['','Dotación','Compra','Donación'],
    'idiomas' => ['es' => 'Español', 'ca' => 'Valencià' , 'en' => 'English'],
    'estadoIncidencia' => ['Rechazada','Pendiente','En proceso','Resuelta'],
    'estadoOrden' => ['Abierta','Cerrada','Resuelta'],
    'prioridadIncidencia' => ['Baja','Media','Alta'],
    'tipoVehiculo' => ['Avion','Tren','Taxi','Autobus','Otros'],
    'estadoDocumento' => ['Creado','Pendiente','Autorizado','Impreso'],
    'numeracion' => ['--','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15',30=>'AvaIni',31=>'1Ava',32=>'2Ava',33=>'3Ava',34=>'AvFinal',35=>'AvExtr',21=>'1er Trimestre',22=>'2on Trimestre',23=>'Final'],
    'nombreEval' => [1=>'Primera', 2=> 'Segona', 3=>'Final', 4=>'Extraordinària'],
    'checkModels' => ['Programacion'=>'Programaciones'],
    'tipoExpediente' => [   1=>'Baixa Inasistència',
                            2=>'Pèrdua Avaluació Continua',
                            3=>'Anul.lació matricula',
                            4=>"Remisió al departament d'Orientació",
                            5=>"Part d'amonestació"
        ],
    'asociacionEmpresa' => [1=>'FCT',2=>'FP DUAL'],
    'tipoTutoria' => [0=>'Tots el grups',1=>'Grau mitjà',2=>'Grau Superior'],
    'actasEnabled' => ['Claustro'=>'Claustro','COCOPE'=>'COCOPE'],
    'motivoAusencia' => ['Baja médica', 'Licencia por formación', 'Enfermedad común', 'Traslado de domicilio', 'Asistencia pruebas selectivas', 'Enfermedad grave o muerte del cónyuge', 'Asistencia médica, educativa o asistencial', 'Otros (rellenar cuadro de observaciones)'],
    'modulosNoLectivos' => ['TU01CF','TU02CF'],
    'tipoEstudio' => [1=>'Cicle Formatiu de Grau Mitjà','2'=>'Cicle Formatiu de Grau Superior','3'=>'Cicle Formatiu Bàsic','4'=>'Batxiller','5'=>'ESO','6'=>'Primària'],
    'tipoEstudioC' => [1=>'Ciclo Formativo de Grado Medio','2'=>'Ciclo Formativo de Grado Superior','3'=>'Ciclo Formativo Básico','4'=>'Bachiller','5'=>'ESO','6'=>'Primaria'],
    'version' => ['v1_0','v1_1'],
    'veep' => [],
    ];

