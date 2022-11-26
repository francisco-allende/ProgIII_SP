<?php

require_once './db/AccesoDatos.php';

class Cripto {
    public $id;
    public $precio;
    public $nombre; 
    public $foto;
    public $nacionalidad; 

    public function __construct() {}

    public static function instanciarCripto($precio, $nombre, $foto, $nacionalidad) {
        $cripto = new Cripto();
        $cripto->setPrecio($precio);
        $cripto->setNombre($nombre);
        $cripto->setFoto($foto);
        $cripto->setNacionalidad($nacionalidad);

        return $cripto;
    }

    //--- Getters ---//

    public function getId(){
        return $this->id;
    }

    public function getPrecio(){
        return $this->precio;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getFoto(){
        return $this->foto;
    }

    public function getNacionalidad(){
        return $this->nacionalidad;
    }

    //--- Setters ---//

    public function setId($id){
        $this->id = $id;
    }

    public function setPrecio($precio){
        $this->precio = $precio;
    }

    public function setNombre($nombre){
        $this->nombre = $nombre;
    }
    
    public function setFoto($foto){
        $this->foto = $foto;
    }

    public function setNacionalidad($nacionalidad){
        $this->nacionalidad = $nacionalidad;
    }

    //--- Database Methods ---///

    public function CrearCripto()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("
        INSERT INTO criptos (precio, nombre, foto, nacionalidad) 
        VALUES (:precio, :nombre, :foto, :nacionalidad)");
        
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }
    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM criptos");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Cripto');
    }
    public static function ObtenerCriptoPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM criptos WHERE id = :id");
        $consulta->bindValue(':id', $id);
        $consulta->execute();

        $myObj = $consulta->fetchObject('Cripto');
        if (is_null($myObj)) {
            return null;
        }

        return $myObj;
    }
    public static function ObtenerCriptoPorPais($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM criptos WHERE nacionalidad = :nacionalidad");
        $consulta->bindValue(':nacionalidad', $nacionalidad);
        $consulta->execute();

        $myObj = $consulta->fetchAll(PDO::FETCH_CLASS, 'Cripto'); //para traer varios, siempre fetch class o retoran el primero
        if (is_null($myObj)) {
            return null;
        }

        return $myObj;
    }

    public static function ModificarCripto($id, $precio, $nombre, $foto, $nacionalidad)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta(
        "UPDATE criptos 
        SET precio = :precio, nombre = :nombre, foto = :foto, nacionalidad = :nacionalidad 
        WHERE id = :id");
        $consulta->bindValue(':precio', $precio, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $foto, PDO::PARAM_STR);
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        if($consulta->rowCount() == 1){
            return true;
        }else{
            return false;
        }
    }

    public static function BorrarCripto($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("DELETE FROM criptos WHERE id = :id;"); 
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        if($consulta->rowCount() == 1){
            return true;
        }else{
            return false;
        }
    }
}
?>