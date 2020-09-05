<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Cleaner;
use App\Repository\CompanyRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->companyRepository = $entityManager->getRepository(Company::class);

    }

    public function load(ObjectManager $manager)
    {
        $this
            ->loadCompanyFixtures($manager)
            ->loadCleanerFixtures($manager);
    }

    public function loadCompanyFixtures(ObjectManager $manager): self {
        $companies = $this->generateCompanies();

        foreach ($companies as $company) {
            $manager->persist($company);
        }

        $manager->flush();

        return $this;
    }

    /**
     * @return Company[]
     */
    public function generateCompanies(): array
    {
        $companies = [];

        for ($i = 0; $i < 5; $i++) {
            $company = new Company();
            $company->setName('Company '.$i);

            $companies[] = $company;
        }

        return $companies;
    }

    public function loadCleanerFixtures(ObjectManager $manager) {
        $companies = $this->companyRepository->findAll();

        $cleaners = $this->generateCleaners($companies);

        foreach ($cleaners as $cleaner) {
            $manager->persist($cleaner);
        }

        $manager->flush();
    }

    /**
     * @param Company[]|array
     *
     * @return Cleaner[]|array
     */
    public function generateCleaners(array $companies): array
    {
        $cleaners = [];

        for ($i = 0; $i < 20; $i++) {
            $company = $companies[mt_rand(0, count($companies) - 1)];

            $cleaner = new Cleaner();
            $cleaner->setName('Cleaner '.$i);
            $cleaner->setCompany($company);

            $cleaners[] = $cleaner;
        }

        return $cleaners;
    }
}
