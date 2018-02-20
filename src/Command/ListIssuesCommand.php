<?php
/**
 * @date    2017-06-13
 * @file    ListIssuesCommand.php
 * @author  Patrick Mac Gregor <macgregor.porta@gmail.com>
 */

namespace Macghriogair\RedReader\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class ListIssuesCommand extends Command
{
    protected function configure()
    {
        $this->setName('red:issues')
            ->setDescription('List issues assigned to me.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = require __DIR__ . '/../../config.php';

        $client = new \Redmine\Client($config['url'], $config['token']);

        $user = $client->user->getCurrentUser()['user'];

        $myIssues = $client->issue->all([
            'assigned_to_id' => $user['id'],
            'sort' => 'priority:desc,updated_on:desc',
            'limit' => 100
        ]);

        $table = new Table($output);
        $table->setHeaders([$myIssues['total_count'], '#', 'Project', 'Subject']);
        foreach ($myIssues['issues'] as $issue) {
            $table->addRow([
                $this->mapPrio($issue['priority']['name']),
                $issue['id'],
                $this->ellipsis($issue['project']['name'], 20),
                $this->ellipsis($issue['subject']),
            ]);
        }

        $table->render();
    }

    protected function ellipsis(string $text, int $limit = 50)
    {
        if (mb_strlen($text) > $limit) {
            return mb_substr($text, 0, $limit) . '..';
        }
        return $text;
    }

    protected function mapPrio(string $prio)
    {
        $prio = strtolower($prio);
        $map = [
            'dringend' => '↑',
            'hoch' => '↗',
            'normal' => '→',
            'niedrig' => '↓'
        ];
        return array_key_exists($prio, $map) ? $map[$prio] : '?';
    }
}
