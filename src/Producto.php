<?php

class Producto
{
    // Encapsulamiento de atributos
    private $tableName, $dataFile, $data;

    public function __construct($tableName)
    {
        try {
            $this->setTableName($tableName);
            $this->loadData();
        } catch (Exception $e) {
            // Manejo de la excepción, podrías registrar el error o mostrar un mensaje
            echo 'Error en el constructor: ' . $e->getMessage();
        }
    }

    // Abstracción de funciones
    public function create($data)
    {
        $this->data[] = $data;  // Añadir al array de datos
        $this->saveData();      // Guardar cambios en el archivo
        return count($this->data);
    }

    public function read($id)
    {
        if (isset($this->data[$id])) {
            return $this->data[$id];
        }
        return null;  // Si no existe, devuelve null
    }

    public function readAll()
    {
        return $this->data;
    }

    public function update($id, $data)
    {
        if (isset($this->data[$id])) {
            $this->data[$id] = $data;
            $this->saveData();  // Guardar cambios en el archivo
            return true;
        }
        return false;  // Si no existe, retorna false
    }

    public function delete($id)
    {
        if (isset($this->data[$id])) {
            unset($this->data[$id]);   // Eliminar el registro
            $this->data = array_values($this->data);  // Reindexar array
            $this->saveData();         // Guardar cambios en el archivo
            return true;
        }
        return false;
    }

    public function setTableName($tableName)
    {
        $this->tableNameValidate($tableName);
        $this->tableName = $tableName;
        $this->dataFile = $tableName . '.json';
    }

    private function loadData()
    {
        try {
            if (file_exists($this->dataFile)) {
                $jsonData = file_get_contents($this->dataFile);
                if ($jsonData === false) {
                    throw new Exception("Error al leer el archivo de datos.");
                }
                $this->data = json_decode($jsonData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Error al decodificar el JSON: " . json_last_error_msg());
                }
            } else {
                $this->data = [];  // Si no existe el archivo, iniciar con un array vacío
            }
        } catch (Exception $e) {
            // Manejo de la excepción, podrías registrar el error o mostrar un mensaje
            echo 'Error al cargar los datos: ' . $e->getMessage();
        }
    }

    private function saveData()
    {
        try {
            $jsonData = json_encode($this->data, JSON_PRETTY_PRINT);
            if (file_put_contents($this->dataFile, $jsonData) === false) {
                throw new Exception("Error al guardar los datos en el archivo.");
            }
        } catch (Exception $e) {
            // Manejo de la excepción
            echo 'Error al guardar los datos: ' . $e->getMessage();
        }
    }

    private function tableNameValidate($tableName)
    {
        if ($tableName == "" || $tableName == null) {
            throw new Exception("Table name required");
        }
        if (!is_string($tableName)) {
            throw new Exception("Table name must be a string");
        }
    }
}