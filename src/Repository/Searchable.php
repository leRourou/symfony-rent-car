<?php

namespace App\Repository;

interface Searchable
{
    public function search($limit = 10, $page = 1, $searchTerm = null);
}
