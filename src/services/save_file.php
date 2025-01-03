<?php

    require_once __DIR__ . "/../helpers/constants.php";
    require_once __DIR__ . "/../helpers/gen_id.php";

    /**
     * Guardar el archivo en el servidor.
     * @param mixed $file Archivo a guardar.
     * @param mixed $uploadDir Directorio donde se guardará el archivo.
     * @throws \Exception Si ocurre un error al guardar el archivo.
     * @return string Nombre del archivo guardado.
     */
    function save_file($file, $uploadDir): string {
        $fileName = $file['name'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
        $fileName = generateID(FILE_PREFIX) . '.' . $fileExt;
        $filePath = $uploadDir . $fileName;
        if(!move_uploaded_file($file['tmp_name'], $filePath)) {
            throw new Exception('Error al subir el archivo');
        }
        return $fileName;
    }

?>