<?php

namespace App\Sorting;

class TeacherSorting implements Sorting
{
    public function sort(string $filter): array
    {
        if ($filter === 'highest') {
            return [
                'property' => 't.rating',
                'criteria' => 'DESC'
            ];
        } elseif ($filter === 'lowest') {
            return [
                'property' => 't.rating',
                'criteria' => 'ASC'
            ];
        } elseif ($filter === 'cheapest') {
            return [
                'property' => 't.pricePerHour',
                'criteria' => 'ASC'
            ];
        } else {
            return [
                'property' => 't.pricePerHour',
                'criteria' => 'DESC'
            ];
        }
    }
}