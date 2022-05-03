<?php

namespace App\DataFixtures;

use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $subjects = ['Български Език', 'Математика', 'Английски език', 'История', 'География', 'Физика', 'Биология'];

        foreach ($subjects as $subject) {
            $subjectEntity = new Subject();
            $subjectEntity->setName($subject);
            $manager->persist($subjectEntity);
        }

        $manager->flush();
    }
}
