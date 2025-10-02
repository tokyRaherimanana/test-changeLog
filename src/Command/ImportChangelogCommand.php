<?php
// src/Command/ImportChangelogCommand.php
namespace App\Command;

use App\Entity\Changelog;
use App\Service\Metier\TrelloServiceSM;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportChangelogCommand extends Command
{
    protected static $defaultName = 'app:changelog:import';
    private EntityManagerInterface $em;
    private TrelloServiceSM $trelloService;

    public function __construct(EntityManagerInterface $em, TrelloServiceSM $trelloService)
    {
        parent::__construct();
        $this->em = $em;
        $this->trelloService = $trelloService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('from', InputArgument::OPTIONAL, 'From git ref', 'HEAD~50')
            ->addArgument('to', InputArgument::OPTIONAL, 'To git ref', 'HEAD')
            ->addArgument('version', InputArgument::OPTIONAL, 'Version label', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getArgument('from');
        $to = $input->getArgument('to');
        $version = $input->getArgument('version');

        $repo = $this->em->getRepository(Changelog::class);

        $cmd = sprintf('git log %s..%s --pretty=format:"%%H||%%s"', escapeshellarg($from), escapeshellarg($to));
        $out = shell_exec($cmd);
        if (!$out) {
            $output->writeln('<comment>Pas de commits trouvés (ou git non disponible)</comment>');
            return Command::SUCCESS;
        }

        $lines = array_filter(array_map('trim', explode("\n", trim($out))));

        foreach ($lines as $line) {
            [$hash, $subject] = explode('||', $line, 2);

            // skip duplicates by hash
            if ($repo->findOneBy(['commitHash' => $hash])) {
                continue;
            }

            // parse commit message
            if (!preg_match('/(feat|fix|chore|docs|refactor|perf)(?:\(\s*(?:TRELLO-)?([A-Za-z0-9_-]+|\#\d+)\s*\))?:\s*(.+)/i', $subject, $m)) {
                $type = 'other';
                $trelloRef = null;
                $desc = $subject;
            } else {
                $type = strtolower($m[1]);
                $trelloRef = $m[2] ?? null;
                $desc = $m[3];
            }

            // author and commit date
            $metaCmd = sprintf('git show -s --pretty=format:"%%an||%%aI" %s', escapeshellarg($hash));
            $metaOut = trim(shell_exec($metaCmd));
            $author = null;
            $dateIso = null;
            if ($metaOut && strpos($metaOut, '||') !== false) {
                [$author, $dateIso] = explode('||', $metaOut, 2);
            }

            // files (numstat)
            $files = [];
            $filesCmd = sprintf('git show --numstat --pretty="" %s', escapeshellarg($hash));
            $filesOut = trim(shell_exec($filesCmd));
            if ($filesOut) {
                $fileLines = array_filter(array_map('trim', explode("\n", $filesOut)));
                foreach ($fileLines as $fline) {
                    $parts = preg_split('/\s+/', $fline, 3);
                    if (count($parts) === 3) {
                        [$add, $del, $path] = $parts;
                        $files[] = [
                            'path' => $path,
                            'add' => is_numeric($add) ? (int)$add : null,
                            'del' => is_numeric($del) ? (int)$del : null,
                        ];
                    }
                }
            }

            $entry = new Changelog();
            $entry->setCommitHash($hash);
            $entry->setType($type);
            $entry->setDescription($desc);
            $entry->setVersion($version);
            $entry->setAuthor($author);
            if ($dateIso) {
                try {
                    $entry->setCommittedAt(new \DateTimeImmutable($dateIso));
                } catch (\Exception $e) {
                    // ignore parse errors
                }
            }
            if (!empty($files)) {
                $entry->setFiles($files);
            }

            if ($trelloRef) {
                $shortLink = ltrim($trelloRef, '#');
                $card = $this->trelloService->getCardByShortLink($shortLink);
                if ($card) {
                    $entry->setTrelloCardId($card['id'] ?? null);
                    $entry->setTrelloCardShortlink($card['shortLink'] ?? null);
                    $entry->setTrelloCardUrl($card['url'] ?? null);
                    $entry->setTrelloCardName($card['name'] ?? null);
                } else {
                    $entry->setTrelloCardShortlink($shortLink);
                }
            }

            $this->em->persist($entry);
        }

        $this->em->flush();
        $output->writeln('<info>Import terminé.</info>');
        return Command::SUCCESS;
    }
}
