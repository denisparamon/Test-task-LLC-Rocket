<?php

// Создаем класс Slave для представления рабов.
class Slave {
    private $name;
    private $isVip;
    private $schedule;

    // Конструктор класса.
    public function __construct($name, $isVip = false) {
        $this->name = $name;
        $this->isVip = $isVip;
        $this->schedule = array(); // Создаем массив для хранения расписания аренды.
    }

    // Метод для аренды раба.
    public function rentSlave($start_time, $end_time) {
        $duration = $end_time->diff($start_time)->h; // Вычисляем продолжительность аренды в часах.

        // Если клиент VIP, он может игнорировать занятые не VIP-ами часы.
        if ($this->isVip) {
            $this->schedule[$start_time->format('Y-m-d H:i:s')] = $duration;
        } else {
            // Проверяем, что выбранный период не перекрывается с уже занятым временем.
            foreach ($this->schedule as $booked_start => $booked_duration) {
                $booked_start = new DateTime($booked_start);
                $booked_end = clone $booked_start;
                $booked_end->add(new DateInterval('PT' . $booked_duration . 'H'));

                if ($start_time < $booked_end && $end_time > $booked_start) {
                    throw new Exception("Выбранный период занят другим клиентом");
                }
            }

            $this->schedule[$start_time->format('Y-m-d H:i:s')] = $duration;
        }
    }
    
    // Метод для расчета стоимости аренды.
    public function calculateRentCost() {
        $totalCost = 0;

        // Проходим по расписанию аренды и вычисляем стоимость.
        foreach ($this->schedule as $start_time => $duration) {
            $start_time = new DateTime($start_time);

            if ($duration > 16) {
                $totalCost += 16 * $this->calculateHourlyRate($start_time);
            } else {
                $totalCost += $duration * $this->calculateHourlyRate($start_time);
            }
        }

        return $totalCost;
    }

    // Метод для расчета почасовой ставки аренды.
    public function calculateHourlyRate($start_time) {
        // Здесь можно добавить логику расчета стоимости аренды в зависимости от времени суток и других факторов.
        // В данном примере, мы используем простой тариф 10 золотых в час.
        return 10;
    }
}

// Создаем объекты Slave.
$slave1 = new Slave("Slave1");
$slave2 = new Slave("Slave2", true); // Slave2 является VIP-клиентом.

// Устанавливаем временные точки начала и конца аренды.
$start_time1 = new DateTime('2016-06-01 14:00');
$end_time1 = new DateTime('2016-06-05 20:00');

$start_time2 = new DateTime('2016-06-03 12:00');
$end_time2 = new DateTime('2016-06-03 14:00');

try {
    // Пытаемся арендовать рабов.
    $slave1->rentSlave($start_time1, $end_time1);
    $slave2->rentSlave($start_time1, $end_time1);
    $slave1->rentSlave($start_time2, $end_time2);
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}

// Рассчитываем стоимость аренды.
$cost1 = $slave1->calculateRentCost();
$cost2 = $slave2->calculateRentCost();

// Выводим результат.
echo "Стоимость аренды Slave1: $cost1 золотых\n";
echo "Стоимость аренды Slave2: $cost2 золотых\n";

?>