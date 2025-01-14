<?php

namespace App\Repository;

interface Searchable
{
    public function search($limit = 10, $searchTerm = null);
}
