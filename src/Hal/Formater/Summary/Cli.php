<?php

/*
 * (c) Jean-François Lépine <https://twitter.com/Halleck45>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hal\Formater\Summary;
use Hal\Bounds\Bounds;
use Hal\Bounds\DirectoryBounds;
use Hal\Bounds\Result\ResultInterface;
use Hal\Formater\FormaterInterface;
use Hal\Result\ResultCollection;
use Hal\Result\ResultSet;
use Hal\Rule\Validator;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Output\ConsoleOutput;


/**
 * Formater for cli usage
 *
 * @author Jean-François Lépine <https://twitter.com/Halleck45>
 */
class Cli implements FormaterInterface {

    /**
     * Validator
     *
     * @var \Hal\Rule\Validator
     */
    private $validator;

    /**
     * Level
     *
     * @var int
     */
    private $level;

    /**
     * Constructor
     *
     * @param Validator $validator
     */
    function __construct(Validator $validator, $level)
    {
        $this->validator = $validator;
        $this->level = $level;
    }

    /**
     * @inheritdoc
     */
    public function pushResult(ResultSet $resultSet) {
    }

    /**
     * @inheritdoc
     */
    public function terminate(ResultCollection $collection){

        $output = new ConsoleOutput();

        $output->writeln('PHPMetrics by Jean-François Lépine <https://twitter.com/Halleck45>');
        $output->writeln('');


        // overview
        $service = new Bounds();
        $total = $service->calculate($collection);
        $output->writeln(sprintf(
            '<info>%d</info> files have been analyzed. Read and understand these <info>%s</info> lines of code will take around <info>%s</info>.'
            , sizeof($collection, COUNT_NORMAL)
            , $total->getSum('loc')
            , $this->formatTime($total->getSum('time'))
        ));


        // by directory
        $service = new DirectoryBounds($this->level);
        $directoryBounds = $service->calculate($collection);


        $output->writeln('<info>Avegare for each module:</info>');
        $output->writeln('');

        $table = new TableHelper();
        $table
            ->setHeaders(array(
                'Directory'
                , 'LOC'
                , 'Complexity'
                , 'Maintenability'
                , 'LLOC'
                , 'Comment weight'
                , 'Vocabulary'
                , 'Volume'
                , 'Bugs'
                , 'Difficulty'
            ))
            ->setLayout(TableHelper::LAYOUT_DEFAULT);

        foreach($directoryBounds as $directory => $bound) {
            $table->addRow(array(
                str_repeat('  ', $bound->getDepth()).$directory
                , $this->getRow($bound, 'loc', 'sum', 0)
                , $this->getRow($bound, 'cyclomaticComplexity', 'average', 0)
                , $this->getRow($bound, 'maintenabilityIndex', 'average', 0)
                , $this->getRow($bound, 'logicalLoc', 'average', 0)
                , $this->getRow($bound, 'commentWeight', 'average', 0)
                , $this->getRow($bound, 'vocabulary', 'average', 0)
                , $this->getRow($bound, 'volume', 'average', 0)
                , $this->getRow($bound, 'bugs', 'average', 2)
                , $this->getRow($bound, 'difficulty', 'average', 0)
            ));
        }
        $table->render($output);


    }

    /**
     * Get formated row
     *
     * @param ResultInterface $bound
     * @param $key
     * @param $type
     * @param $round
     * @return string
     */
    private function getRow(ResultInterface $bound, $key, $type, $round) {
        $value = round($bound->get($type, $key), $round);
        return sprintf('<%1$s>%2$s</%1$s>', $this->getStyle($key, $value), $value);
    }

    /**
     * Get style, according score
     *
     * @param $key
     * @param $value
     * @return null|string
     */
    private function getStyle($key, $value) {
        $score = $this->validator->validate($key, $value);

        switch($score) {
            case Validator::GOOD:
                return 'fg=green';
            case Validator::WARNING:
                return 'bg=yellow;fg=black';
            case Validator::CRITICAL:
                return 'bg=red;fg=white';
        }
        return 'fg=white';
    }

    /**
     * Format time in text
     *
     * @param $v
     * @return string
     */
    private function formatTime($v) {
        return sprintf('%s hour(s), %s minute(s) and %s second(s)'
            , gmdate('H', $v)
            , gmdate('m', $v)
            , gmdate('s', $v)
        );
    }
}