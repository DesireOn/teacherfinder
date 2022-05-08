<?php

namespace App\Sorting;

class ReviewSorting implements Sorting
{
    public function sort(string $filter): array
    {
        if ($filter === 'oldest') {
            return [
                'property' => 'r.date',
                'criteria' => 'ASC'
            ];
        } elseif ($filter === 'highest') {
            return [
                'property' => 'r.rating',
                'criteria' => 'DESC'
            ];
        } elseif ($filter === 'lowest') {
            return [
                'property' => 'r.rating',
                'criteria' => 'ASC'
            ];
        } else {
            return [
                'property' => 'r.date',
                'criteria' => 'DESC'
            ];
        }
    }
}