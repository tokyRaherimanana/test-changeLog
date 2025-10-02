<?php

namespace App\Services\Applicatif;

use App\Entity\Personne;
use App\Services\Metier\PersonneSM;

class PersonneSA
{
    private PersonneSM $personneSM;

    public function __construct(
        PersonneSM $personneSM
    )
    {
        $this->personneSM = $personneSM;
    }

    public function create(array $data){
        return $this->personneSM->create($data);
    }

    public function update(Personne $personne, array $data){
        return $this->personneSM->update($personne, $data);
    }
}
