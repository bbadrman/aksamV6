<?php

namespace App\Search;

use DateTime;

class SearchProspect
{
    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var string
     */
    public $q = '';

    /**
     * @var string
     */
    public $m = '';

    /**
     * @var string
     */
    public $g = '';

    /**
     * @var string
     */
    public $l = '';

    /**
     * @var string
     */
    public  $team = '';

    /**
     * @var string
     */
    public $c = '';

    /**
     * @var string
     */
    public $r = '';
    /**
     * @var string
     */
    public $s = '';

    /**
     * @var string
     */
    public $d;

    /**
     * @var string
     */
    public $dd;
    /**
     * @var int
     */
    /**
     * @var string
     */
    public $dr;

    /**
     * @var string
     */
    public $ddr;
    /**
     * @var int
     */
    public $source;
    /**
     * @var int
     */
    public $motifRelanced;
    /**
     * @var int|null
     */
    public $y;
    /**
     * Récupère l'année de la recherche
     * 
     * @return int|null L'année de la recherche ou null si non définie
     */
    public function getYear(): ?int
    {
        // Récupérer l'année depuis la propriété de la classe
        return $this->y;
    }

    private $month;

    public function setMonth($month)
    {
        $this->month = $month;
    }

    public function getMonth()
    {
        return $this->month;
    }
}
