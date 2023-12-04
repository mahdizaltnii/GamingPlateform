<?php

namespace App\Data;

use App\Entity\AnnonceCat;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchData
{

    /**
     * @var string
     */
    public $q='';

    /**
     * @var AnnonceCat[]
     */
    public $categorie= [];

    /**
     * @var null|integer
     */
    public $max;

    /**
     * @var null|integer
     */
    public $min;

}