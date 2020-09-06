<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Opinion;
use DateTime;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    /**
     * Utilisation du constructeur pour récupérer le service de hashage des mots de passe via autowiring
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        // Boucle de 10 itérations
        for($i = 1; $i <= 12; $i++){

            // Création d'un nouvel user
            $newUser = new User();

            // Hydratation du nouvel user
            $newUser
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($newUser, 'Alicedu71@'))
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
                ->setIsVerified(1)
            ;

            // Enregistrement du nouvel user auprès de Doctrine
            $manager->persist($newUser);

            // Stockage des comptes de côté pour créer des avis plus bas
            $users[] = $newUser;
        }
            // Boucle de 10 itérations
            for($i = 1; $i <= 12; $i++){

            // Création d'un nouvel avis
            $newOpinion = new Opinion();

            // Hydratation du nouvel avis
            $newOpinion
                ->setAuthor($faker->randomElement($users))
                ->setTitle( $faker->sentence) // Phrase aléatoire
                ->setMark($faker->numberBetween($min = 1, $max = 5))
                ->setContent( $faker->sentence ) // Paragraphe de 1 phrase aléatoire
                ->setPublicationDate( $faker->dateTimeBetween('-5years', 'now') )   // Date aléatoire entre il y a 5 ans et maintenant
            ;

            // Enregistrement du nouvel avis auprès de Doctrine
            $manager->persist($newOpinion);
        }
        $manager->flush();
    }
}
