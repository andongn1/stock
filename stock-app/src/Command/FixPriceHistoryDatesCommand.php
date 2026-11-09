<?php

namespace App\Command;

use App\Repository\PriceHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'price-history:fix-dates',
    description: 'Fix price history dates to ensure valid_from is not in the future and valid_to is properly set'
)]
class FixPriceHistoryDatesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PriceHistoryRepository $priceHistoryRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->info('Fixing price history dates...');

        $now = new \DateTime();
        $fixedCount = 0;
        $alreadyValidCount = 0;

        $priceHistories = $this->priceHistoryRepository->findAll();

        $io->text("Found " . count($priceHistories) . " price history records.");

        foreach ($priceHistories as $priceHistory) {
            $needsUpdate = false;

            $validFrom = $priceHistory->getValidFrom();
            if ($validFrom && $validFrom > $now) {
                $priceHistory->setValidFrom($now);
                $needsUpdate = true;
                $io->text("Fixed valid_from for price ID " . $priceHistory->getId() . " (was: " . $validFrom->format('Y-m-d') . ")");
            }

            $validTo = $priceHistory->getValidTo();
            if ($validTo && $validTo > $now) {
                $priceHistory->setValidTo($now);
                $needsUpdate = true;
                $io->text("Fixed valid_to for price ID " . $priceHistory->getId() . " (was: " . $validTo->format('Y-m-d') . ")");
            }

            if ($needsUpdate) {
                $this->entityManager->flush();
                $fixedCount++;
            } else {
                $alreadyValidCount++;
            }
        }

        $io->success("Done! Fixed {$fixedCount} records, {$alreadyValidCount} were already valid.");

        return Command::SUCCESS;
    }
}
