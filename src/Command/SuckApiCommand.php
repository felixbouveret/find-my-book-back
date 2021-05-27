<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\Livres;
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Psr\Container\ContainerInterface;

class SuckApiCommand extends Command
{
    protected static $defaultName = 'suck-api';
    protected static $defaultDescription = 'Get into the API and suck the content';
    protected $headers = ["Content-Type" => "application/json"];
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
            $io->note(sprintf('You passed an option: %s', $input->getOption('search')));
        }
        $search = $input->getOption('search');

        $client = new Client([
            'base_uri' => 'http://www.googleapis.com/books/v1/',
            'timeout'  => 2.0,
        ]);

        $request = new Request(
            'GET', 
            'https://www.googleapis.com/books/v1/volumes?q='.$search.'&maxResults=40', 
            $this->headers, 
        );

        try {
            $io->note("Connection API...");
            $io->note("Récupération des données...");
            $response = $client->send($request);
        } catch (\Exception $e) {
            $io->warning($e->getMessage());
        }
        $ret = json_decode($response->getBody());
        $io->note("Données récupérées !");
        $array_books = $ret->items;
        $final_array = [];
        $io->note("Création de l'input...");
        foreach ($array_books as $book) {
            $new_request = new Request(
                'GET', 
                $book->selfLink, 
                $this->headers, 
            );
            try {
                $info_book = json_decode($client->send($new_request)->getBody());
                array_push($final_array, [
                    "auteur" => isset($info_book->volumeInfo->authors[0]) ? $info_book->volumeInfo->authors[0] : "Non connu",
                    "titre" => $info_book->volumeInfo->title,
                    "synopsis" => isset($info_book->volumeInfo->description) ? $info_book->volumeInfo->description : "Aucun",
                    "image_url" => "https://books.google.com/books/content/images/frontcover/".$book->id."?fife=w400-h600",
                    "isbn_code" => $book->id
                ]);
            } catch(\Exception $e) {
                $io->warning($e->getMessage() . ' Pour : ' . var_dump($info_book));
            }
        }
        $io->note("Input crée !");
        $io->note("Connection BDD");
        foreach ($final_array as $book_data) {
            $book = new Livres();
            $book->setName($book_data['titre']);
            $book->setIsbnCode($book_data['isbn_code']);
            $book->setAuteur($book_data['auteur']);
            $book->setSynopsis($book_data['synopsis']);
            $book->setImgUrl($book_data['image_url']);
            $book->setCategory(1);
            $this->em->persist($book);
            $this->em->flush();
            $io->note("Ajout de ". $book_data['titre'] . " OK");
        }
        

        $io->success('Tout les livres ' . $search . ' ont étés ajoutés');

        return Command::SUCCESS;
    }
}