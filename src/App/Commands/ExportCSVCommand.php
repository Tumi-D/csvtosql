<?php

namespace Console\App\Commands;

use DummyDb;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use TestdbModel;

class ExportCSVCommand extends Command
{
    protected function configure()
    {
        $this->setName('export:sql')
            ->setDescription('Export csv to sql.')
            ->setHelp('Exports  you to delete the application cache. Pass the --groups parameter to clear caches of specific groups.')
            ->addArgument('filename', InputArgument::REQUIRED, 'Pass the name of the csv file.')
            // ->addArgument('table', InputArgument::REQUIRED, 'Pass the name of the table you are writing to.')
            ->addOption(
                'table',
                't',
                InputOption::VALUE_NONE,
                "Pass the name of the table you are writing to."
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Csv file is about to be migrated..');
        $filename = $input->getArgument('filename');
        $table = $input->getOption('table') ? $input->getOption('table') : $filename;


        $filepath = dirname(dirname(dirname(dirname(__FILE__)))) . "\app\storage\\" . "$filename.csv";
        $output->writeln(sprintf('<info>filepath--- %s</info>', $filepath));
        $row = 0;
        if (file_exists($filepath)) {

            if (($handle = fopen($filepath, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $num = count($data);
                    $medinfo = new TestdbModel();

                    // dd($medinfo);
                    for ($c = 0; $c < $num; $c++) {
                        $output->writeln(sprintf('<info>%s---%d</info>', $data[$c], $c));
                        $output->writeln(sprintf('<info></info>'));
                    }

                    // $table == "hlt" ?  $value_info = ['hlt_code' => trim("$data[0]"), 'hlt_name' => trim("$data[1]")] : "";
                    // $table == "hlgt" || $table == "hlgt_hlt" ?  $value_info = ['hlgt_code' => trim("$data[0]"), 'hlt_code' => trim("$data[1]")] : "";
                    $table == "sases"  ?  $value_info = ['data' => trim("$data[0]")] : [];
                    // $table == "mdhier" ?  $value_info = ['pt_code' => trim("$data[0]"), 'hlt_code' => trim("$data[1]"), 'hlgt_code' => trim("$data[2]"), 'soc_code' => trim("$data[3]"), 'pt_name' => trim("$data[4]"), 'hlt_name' => trim("$data[5]"), 'hlgt_name' => trim("$data[6]"), 'soc_name' => trim("$data[7]"), 'soc_abbrev' => trim("$data[8]"), 'pt_soc_code' => trim("$data[10]"), 'primary_soc_fg' => trim("$data[11]")] : "";
                    // $table == "pt" ?  $value_info = ['pt_code' => trim("$data[0]"), 'pt_name' => trim("$data[1]"), 'pt_soc_code' => trim("$data[3]")] : "";
                    // $table == "soc" ?  $value_info = ['soc_code' => trim("$data[0]"), 'soc_name' => trim("$data[1]"), 'soc_abbrev' => trim("$data[2]")] : "";
                    // $table == "soc_hlgt" ?  $value_info = ['soc_code' => trim("$data[0]"), 'hlgt_code' => trim("$data[1]")] : "";
                    // $table == "intl_ord" ?  $value_info = ['intl_ord_code' => trim("$data[0]"), 'soc_code' => trim("$data[1]")] : "";
                    // $table == "llt" ?  $value_info = ['llt_code' => trim("$data[0]"), 'llt_name' => trim("$data[1]"), 'pt_code' => trim("$data[2]"), 'J' => trim("$data[9]")] : "";
                    // $table == "smq_list" ?  $value_info = ['smq_code' => trim("$data[0]"), 'smq_name' => trim("$data[1]"), 'smq_level' => trim("$data[2]"), 'smq_description' => trim("$data[3]"), 'smq_source' => trim("$data[4]"), 'smq_note' => trim("$data[5]"), 'MedDRA_version' => trim("$data[6]"), 'status' => trim("$data[7]"), 'smq_algorithm' => trim("$data[8]")] : "";
                    // $table == "smq_content" ?  $value_info = ['smq_code' => trim("$data[0]"), 'term_code' => trim("$data[1]"), 'term_level' => trim("$data[2]"), 'term_scope' => trim("$data[3]"), 'term_category' => trim("$data[4]"), 'term_weight' => trim("$data[5]"), 'term_status' => trim("$data[6]"), 'term_addition_version' => trim("$data[7]"), 'term_last_ modified_version' => trim("$data[8]")] : "";
                    $querystate =   $medinfo->setTable($table)->fill($value_info);
                    $medinfo->save();
                    $querystate ?   $output->writeln('<info>Inserted successfully</info>') : $output->writeln('<info>Insertion failed</info>');
                    $row++;
                }
                fclose($handle);
            }
        }

        $output->writeln("Completed $table." . $row);
        return 1;
    }

    protected function save($table, $values)
    {
        $values = "'" . implode("','", $values) . "'";
        // $values = implode(",", array_map(function($x) use ($dbc,$values) {
        //     return "'" . $dbc->real_escape_string($x) . "'";
        // }, $values);
        global $connectedDb;
        $query = "INSERT INTO $table
        VALUES ($values);";
        $connectedDb->prepare($query);
        return  $connectedDb->execute();
    }
}
