<?php
    /**
     * Genera un UUID versión 4.
     *
     * @return string UUID v4 en formato string.
     */
    function generateUUIDv4() {
        $data = random_bytes(16);

        // Ajustar versión a 0100
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Ajustar variante a 10xx
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return sprintf(
            '%08x-%04x-%04x-%04x-%12x',
            // Hexadecimal de los datos binarios
            unpack('N1', substr($data, 0, 4))[1],
            unpack('n1', substr($data, 4, 2))[1],
            unpack('n1', substr($data, 6, 2))[1],
            unpack('n1', substr($data, 8, 2))[1],
            bin2hex(substr($data, 10, 6))
        );
    }
?>
