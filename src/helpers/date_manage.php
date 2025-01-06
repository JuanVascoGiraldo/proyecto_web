<?php
        /**
         * Obtiene la hora actual en formato UTC.
         *
         * @return DateTime La hora actual como objeto DateTime en UTC. Se selecciona la zona horaria UTC por facilidad.
         */
        function getCurrentUTC(): DateTime {
            return new DateTime('now', new DateTimeZone('UTC'));
        }

        /**
         * Verifica si entre dos fechas hay más de 24 horas de diferencia.
         *
         * @param DateTime $date1 La primera fecha.
         * @param DateTime $date2 La segunda fecha.
         * @return bool Verdadero si hay más de 24 horas de diferencia, falso en caso contrario.
         */
        function isMoreThan24Hours(DateTime $date1, DateTime $date2 = null): bool {
            if ($date2 === null) {
                $date2 = getCurrentUTC();
            }
            $interval = $date1->diff($date2);

            // Verificar si hay más de 1 día completo de diferencia o 24 horas exactas
            return $interval->days > 0 || $interval->h >= 24;
        }

        /**
         * Añade minutos a una fecha.
         * @param DateTime $date1 Fecha a la que se le añadirán los minutos.
         * @param int $minutes Minutos a añadir.
         * @return DateTime Fecha con los minutos añadidos.
         */
        function addMinutesToDate(DateTime $date1, int $minutes): DateTime {
            $date1 ->modify('+'. $minutes .' minutes');
            return $date1;
        }


?>
