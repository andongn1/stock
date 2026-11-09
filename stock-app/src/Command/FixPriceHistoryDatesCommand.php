<?php

namespace App\Command;

use App\Entity\PriceHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixPriceHistoryDatesCommand extends Command
{
    protected static $defaultName = 'app:fix-price-history-dates';
    
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Fixes price history dates that may be in the future');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $priceHistories = $this->em->getRepository(PriceHistory::class)->findAll();
        $fixedCount = 0;
        
        foreach ($priceHistories as $priceHistory) {
            $validFrom = $priceHistory->getValidFrom();
            $validTo = $priceHistory->getValidTo();
            $now = new \DateTime();
            
            if ($validFrom > $now) {
                $priceHistory->setValidFrom($now);
                $fixedCount++;
            }
            
            if ($validTo && $validTo > $now) {
                $priceHistory->setValidTo(null);
                $fixedCount++;
            }
        }
        
        if ($fixedCount > 0) {
            $this->em->flush();
            $io->success("Fixed {$fixedCount} price history dates.");
        } else {
            $io->info('No price history dates needed fixing.');
        }
        
        return Command::SUCCESS;
    }
}
