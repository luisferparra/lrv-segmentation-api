<?php
/**
 * Variables de configuración del entorno
 */
return [
    /**
     * Variable que define el postfix de las tablas de segmentación
     */
    'table_val_postfix'=>'_vals',
    /**
     * Ahora mismo la ponermos a true, pero deberemos gestionarla según usuario que se registra
     * Variable que dice si la api puede crear tablas y segmentaciones
     */
    'allow_create_table_api'=>true,

    /**
     * Nombre de la tabla que tiene realmente id_channel==>bbdd asociada
     */
    'table_bbdd_control' =>'bbdd_users',
];