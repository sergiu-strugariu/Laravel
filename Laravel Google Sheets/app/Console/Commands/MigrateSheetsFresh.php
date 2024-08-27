<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Revolution\Google\Sheets\Facades\Sheets;

class MigrateSheetsFresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:sheets:fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all the sheets application need.';

    /**
     * The sheet id.
     *
     * @var string
     */
    private $sheet;

    public function __construct()
    {
        $this->sheet = getenv('GOOGLE_SHEET_ID');
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Sheets tables;
        $tables = ['Utilizatori', 'Concursuri', 'Lacuri', 'Manse', 'Inscrieri', 'Cantariri', 'Standuri', 'Login Tokens'];

        // Get the list of the sheets.
        $sheets = Sheets::spreadsheet($this->sheet)->sheetList();

        foreach ($tables as $table) {
            $found = false;

            foreach ($sheets as $sheet) {
                if ($sheet === $table) {
                    $this->update($sheet);
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $this->create($table);
            }
        }
    }

    /**
     * If table does not exists its creating it.
     */
    public function create($sheet)
    {
        switch ($sheet) {
            case 'Utilizatori':
                $headers = [['id', 'Prenume', 'Nume', 'Tip', 'Email', 'Mobil', 'Data Nasterii', 'Sex', 'Google ID', 'Istoric Asociere','updated_at', 'created_at']];
                break;
            case 'Concursuri':
                $headers = [['id', 'Nume', 'ID Organizator', 'Nume Organizator', 'Descriere Concurs', 'Regulament Concurs', 'Start Concurs', 'Inchidere Concurs', 'Poza Concurs', 'updated_at', 'created_at']];
                break;
            case 'Lacuri':
                $headers = [['id', 'Nume', 'updated_at', 'created_at']];
                break;
            case 'Manse':
                $headers = [['id', 'ID Concurs', 'ID Lac', 'Nume Concurs', 'Nume Lac', 'Nume Mansa', 'Start Mansa', 'Final Mansa', 'Status Mansa', 'Participanti', 'Participanti Maximi', 'updated_at', 'created_at']];
                break;
            case 'Inscrieri':
                $headers = [['id', 'ID Masa', 'ID Pescar', 'Status Inscriere', 'Nume Concurs', 'Nume Mansa', 'Nume Pescar', 'ID Stand', 'Sector', 'Nume Lac', 'Nume Stand', 'Punce Penalizare', 'Nume Trofeu', 'updated_at', 'created_at']];
                break;
            case 'Cantariri':
                $headers = [['id', 'ID Inscriere', 'ID Stand', 'Nume Concurs', 'Nume Lac', 'Nume Mansa', 'Nume Stand', 'Nume Pescar', 'Time Stamp', 'Cantitate', 'Numar Pesti', 'updated_at', 'created_at']];
                break;
            case 'Standuri':
                $headers = [['id', 'Nume Stand', 'ID Lac', 'Nume Lac', 'updated_at', 'created_at']];
                break;
            case 'Login Tokens':
                $headers = [['id', 'ID Utilizator', 'token', 'used', 'expire_at', 'created_at']];
                break;
            default:
                $headers = [['Eroare!']];
        }

        $sheets = Sheets::spreadsheet($this->sheet);
        $sheets->addSheet($sheet);

        $sheet = $sheets->sheet($sheet);
        $sheet->update($headers);

        $this->info('Sheet created successfully');
    }

    /**
     * If table aleardy exists is going to clear the data and reinitialize it.
     */
    public function update($sheet)
    {
        switch ($sheet) {
            case 'Utilizatori':
                $headers = [['id', 'Prenume', 'Nume', 'Tip', 'Email', 'Mobil', 'Data Nasterii', 'Sex', 'Google ID', 'Istoric Asociere', 'updated_at', 'created_at']];
                break;
            case 'Concursuri':
                $headers = [['id', 'Nume', 'ID Organizator', 'Nume Organizator', 'Descriere Concurs', 'Regulament Concurs', 'Start Concurs', 'Inchidere Concurs', 'Poza Concurs', 'updated_at', 'created_at']];
                break;
            case 'Lacuri':
                $headers = [['id', 'Nume', 'updated_at', 'created_at']];
                break;
            case 'Manse':
                $headers = [['id', 'ID Concurs', 'ID Lac', 'Nume Concurs', 'Nume Lac', 'Nume Mansa', 'Start Mansa', 'Final Mansa', 'Status Mansa', 'Participanti', 'Participanti Maximi', 'updated_at', 'created_at']];
                break;
            case 'Inscrieri':
                $headers = [['id', 'ID Masa', 'ID Pescar', 'Status Inscriere', 'Nume Concurs', 'Nume Mansa', 'Nume Pescar', 'ID Stand', 'Sector', 'Nume Lac', 'Nume Stand', 'Punce Penalizare', 'Nume Trofeu', 'updated_at', 'created_at']];
                break;
            case 'Cantariri':
                $headers = [['id', 'ID Inscriere', 'ID Stand', 'Nume Concurs', 'Nume Lac', 'Nume Mansa', 'Nume Stand', 'Nume Pescar', 'Time Stamp', 'Cantitate', 'Numar Pesti', 'updated_at', 'created_at']];
                break;
            case 'Standuri':
                $headers = [['id', 'Nume Stand', 'ID Lac', 'Nume Lac', 'updated_at', 'created_at']];
                break;
            case 'Login Tokens':
                $headers = [['id', 'ID Utilizator', 'token', 'used', 'expire_at', 'created_at']];
                break;
            default:
                $headers = [['Eroare!']];
        }

        $sheets = Sheets::spreadsheet($this->sheet);
        $sheet = $sheets->sheet($sheet);

        $sheet->clear();
        $sheet->update($headers);
        $this->info('Sheet Updated successfully');
    }
}
