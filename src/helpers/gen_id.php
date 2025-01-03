<?php
    /**
     * Genera un id unico, con un prefix opcional.
     *
     * @param string $prefix Prefijo opcional.
     * @return string ID unico.
     */
    function generateID(String $prefix = "") {
        return uniqid($prefix, true);
    }
?>
