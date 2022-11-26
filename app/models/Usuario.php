<?php

require_once './db/AccesoDatos.php';

class Usuario{
    
    //--- Atributos ---//
    public $id;
    public $mail;
    public $tipo;
    public $clave;

    //--- Constructor ---//
    public function __construct(){}

    public static function instanciarUsuario($mail, $tipo, $clave){
        $usuario = new Usuario();
        $usuario->setMail($mail);
        $usuario->setTipo($tipo);
        $usuario->setClave($clave);

        return $usuario;
    }

    //--- Getters ---//
    public function getId(){
        return $this->id;
    }

    public function getMail(){
        return $this->mail;
    }

    public function getTipo(){
        return $this->tipo;
    }

    public function getClave(){
        return $this->clave;
    }

    //--- Setters ---//

    public function setId($id){
        $this->id = $id;
    }

    public function setMail($mail){
        $this->mail = $mail;
    }

    public function setTipo($tipo){
        $this->tipo = $tipo;
    }

    public function setClave($clave){
        $this->clave = $clave;
    }

    //--- Database Methods ---///

    public function CrearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();

        $consulta = $objAccesoDatos->prepararConsulta("
        INSERT INTO usuarios (mail, tipo, clave) VALUES (:mail, :tipo, :clave)");
        
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $hashClave = password_hash($this->getClave(), PASSWORD_DEFAULT);
        $consulta->bindValue(':clave', $hashClave, PDO::PARAM_STR);
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    }

    public static function ObtenerUsuarioPorMail($mail)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE mail = :mail");
        $consulta->bindValue(':mail', $mail);
        $consulta->execute();

        $myObj = $consulta->fetchObject('Usuario');
        if (is_null($myObj)) {
            return null;
        }

        return $myObj;
    }

    public static function ModificarUsuario($mail, $tipo, $clave, $id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET mail = :mail, clave = :clave WHERE id = :id");
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $password, PDO::PARAM_STR);
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();

        if($consulta->rowCount() == 1){
            return true;
        }else{
            return false;
        }
    }

    public static function BorrarUsuario($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        //$consulta = $objAccesoDato->prepararConsulta("DELETE FROM usuarios WHERE id = :id;"); Descomentar para borrar enserio
        $consulta = $objAccesoDato->prepararConsulta("UPDATE usuarios SET fecha_fin = :fecha_fin WHERE id = :id;");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fecha_fin', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();

        if($consulta->rowCount() == 1){
            return true;
        }else{
            return false;
        }
    }
}
?>