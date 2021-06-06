<?php

namespace App\Command;

use App\Entity\Categorie;
use App\Entity\Livres;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SuckApiCommand extends Command
{
    protected static $defaultName = 'suck-api';
    protected static $defaultDescription = 'Get into the API and suck the content';
    protected $headers = ['Content-Type' => 'application/json'];
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addOption('search', null, InputOption::VALUE_REQUIRED, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('search')) {
            $io->info(sprintf('You passed an option: %s', $input->getOption('search')));
        }
        $search = $input->getOption('search');

        $client = new Client([
            'base_uri' => 'http://www.googleapis.com/books/v1/',
            'timeout' => 2.0,
        ]);

        $request = new Request(
            'GET',
            'https://www.googleapis.com/books/v1/volumes?q='.$search.'&maxResults=40',
            $this->headers,
        );

        try {
            $io->info('Connection API...');
            $io->info('Récupération des données...');
            $response = $client->send($request);
        } catch (\Exception $e) {
            $io->warning($e->getMessage());
        }
        $ret = json_decode($response->getBody());
        $io->info('Données récupérées !');
        $array_books = $ret->items;
        $final_array = [];
        $io->info("Création de l'input...");
        foreach ($array_books as $book) {
            try {
                array_push($final_array, [
                    'auteur' => isset($book->volumeInfo->authors[0]) ? $book->volumeInfo->authors[0] : 'Non connu',
                    'titre' => $book->volumeInfo->title,
                    'synopsis' => isset($book->volumeInfo->description) ? $book->volumeInfo->description : 'Aucun',
                    'image_url' => isset($book->volumeInfo->imageLinks->thumbnail) ? $book->volumeInfo->imageLinks->thumbnail : 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/No-Image-Placeholder.svg/1200px-No-Image-Placeholder.svg.png',
                    'isbn_code' => $book->id,
                    'cat' => isset($book->volumeInfo->categories[0]) ? $book->volumeInfo->categories[0] : 'Non Catégorisé',
                ]);
            } catch (\Exception $e) {
                $io->warning($e->getMessage().' Pour : '.var_dump($book));
            }
        }
        $io->info('Input crée !');
        $io->info('Connection BDD');

        foreach ($final_array as $book_data) {
            $cat = $this->em->getRepository(Categorie::class);
            $resultCat = $cat->searchCat($book_data['cat']);

            if (!$resultCat) {
                $catDb = new Categorie();
                $catDb->setLabel($book_data['cat']);
                $this->em->persist($catDb);
                $this->em->flush();
            }

            $idCat = $resultCat ? $resultCat->getId() : $catDb->getId();

            $book = new Livres();
            $book->setName($book_data['titre']);
            $book->setIsbnCode($book_data['isbn_code']);
            $book->setAuteur($book_data['auteur']);
            $book->setSynopsis($book_data['synopsis']);
            $book->setImgUrl($book_data['image_url']);
            $book->setCategory($idCat);
            $this->em->persist($book);
            $this->em->flush();
            $io->note('Ajout de '.$book_data['titre'].' OK');
        }

        $io->success('Tout les livres '.$search.' ont étés ajoutés');

        return Command::SUCCESS;
    }
}
