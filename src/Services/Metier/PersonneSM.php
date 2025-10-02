<?php

namespace App\Services\Metier;

use App\Entity\Personne;
use Doctrine\ORM\EntityManagerInterface;

class PersonneSM
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    public function create(array $data)
    {
        try {
            $personne = new Personne();
            $personne
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setAdresse($data['adresse'])
                ->setTelephone($data['telephone']);

            $this->em->persist($personne);
            $this->em->flush();

            return ['success' => true, 'msg' => 'enregistrement success'];
        } catch (\Exception $ex) {
            return ['success' => false, 'msg' => $ex->getMessage()];
        }
    }

    public function update(Personne $personne, array $data)
    {
        try {
            $personne
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setAdresse($data['adresse'])
                ->setTelephone($data['telephone']);

            $this->em->persist($personne);
            $this->em->flush();

            return ['success' => true, 'msg' => 'modification success'];
        } catch (\Exception $ex) {
            return ['success' => false, 'msg' => $ex->getMessage()];
        }
    }
}
