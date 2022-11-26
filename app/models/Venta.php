<?php

require_once './db/AccesoDatos.php';

class Venta {

    public $id;
    public $fecha;
    public $cantidad;
    public $nombre_cripto;
    public $usuario_comprador;
    public $foto;

    public function __construct() {}

     public static function instanciarVenta($fecha, $cantidad, $nombre_cripto, $usuario_comprador, $foto) {
        $venta = new Venta();
        $venta->setFecha($fecha);
        $venta->setCantidad($cantidad);
        $venta->setNombreCripto($nombre_cripto);
        $venta->setUsuarioComprador($usuario_comprador);
        $venta->setFoto($foto);

        return $venta;
     }

    //--- Getters ---//

    public function getId() {
        return $this->id;
    }

    public function getFecha() {
        return $this->fecha;
    }

    public function getCantidad() {
        return $this->cantidad;
    }

    public function getNombreCripto() {
        return $this->nombre_cripto;
    }

    public function getUsuarioComprador() {
        return $this->usuario_comprador;
    }

    public function getFoto() {
        return $this->foto;
    }

    //--- Setters ---//

    public function setId($id) {
        $this->id = $id;
    }

    public function setFecha($fecha) {
        $this->fecha = $fecha;
    }

    public function setCantidad($cantidad) {
        $this->cantidad = $cantidad;
    }

    public function setNombreCripto($nombre_cripto) {
        $this->nombre_cripto = $nombre_cripto;
    }

    public function setUsuarioComprador($usuario_comprador) {
        $this->usuario_comprador = $usuario_comprador;
    }

    public function setFoto($foto) {
        $this->foto = $foto;
    }

    //--- Database Methods ---///

    public function CrearVenta()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("
        INSERT INTO ventas (fecha, cantidad, nombre_cripto,	usuario_comprador, foto) 
        VALUES (:fecha, :cantidad, :nombre_cripto, :usuario_comprador, :foto)");
        
        $consulta->bindValue(':fecha', $this->fecha);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_STR);
        $consulta->bindValue(':nombre_cripto', $this->nombre_cripto, PDO::PARAM_STR);
        $consulta->bindValue(':usuario_comprador', $this->usuario_comprador, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta');
    }
    public static function ObtenerVentaPorPaisYFecha($nacionalidad, $fecha_inicio, $fecha_fin)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta(
            "SELECT * FROM `ventas` AS v
            INNER JOIN `criptos` AS c
            ON c.nacionalidad = :nacionalidad AND v.nombre_cripto = c.nombre
            WHERE v.fecha BETWEEN :fecha_inicio AND :fecha_fin;"
        );
        $consulta->bindValue(':nacionalidad', $nacionalidad);
        $consulta->bindValue(':fecha_inicio', $fecha_inicio);
        $consulta->bindValue(':fecha_fin', $fecha_fin);
        $consulta->execute();

        $myObj = $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta'); //para traer varios, siempre fetch class o retoran el primero
        if (is_null($myObj)) {
            return null;
        }

        return $myObj;
    }
    
    public static function ObtenerVentaPorNombreCripto($nombre_cripto)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM `ventas` where nombre_cripto = :nombre_cripto");
        $consulta->bindValue(':nombre_cripto', $nombre_cripto);
        $consulta->execute();

        $myObj = $consulta->fetchAll(PDO::FETCH_CLASS, 'Venta'); 
        if (is_null($myObj)) {
            return null;
        }

        return $myObj;
    }
}
?>