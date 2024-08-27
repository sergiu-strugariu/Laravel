<?php

namespace App\Models;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Revolution\Google\Sheets\Facades\Sheets as FacadesSheets;

class Sheets extends Model
{
    use HasFactory;

    private $sheet;
    private static $cache = [];


    public function __construct()
    {
        $this->sheet = FacadesSheets::spreadsheet(getenv("GOOGLE_SHEET_ID"));
    }

    // Returns all the rows of a specific sheet
    public static function get(string $sheet, bool $headers = false, bool $resetCache = false)
    {

        if (!$resetCache && key_exists($sheet, self::$cache)) {
            if (key_exists($headers, self::$cache[$sheet])) {
                return self::$cache[$sheet][$headers];
            }
        }

        // Get all the rows of a sheet
        $rows = (new self())->sheet->sheet($sheet)->get();

        // Check if headers is true, if is true return the rows without removing the headers.
        if ($headers) {
            return $rows->toArray();
        }

        // Get the header of the specific sheet
        $header = $rows->pull(0);

        // Get only the rows, without header
        $rows = (new self())->sheet->collection(header: $header, rows: $rows);

        self::$cache[$sheet][$headers] = $rows->toArray();
        // Return the rows without headers
        return $rows->toArray();
    }

    // Insert a row into a sheet
    public static function insert(string $sheet, array $row = [])
    {
        // Get the specific sheet
        $sheet = (new self())->sheet->sheet($sheet);

        // Insert the data asa a new row in the sheet
        $sheet->append([$row]);

        // If the insertions is has a error return fasle
        if (!$sheet) {
            return false;
        }

        // Return true if everything worked perfectlly
        return true;
    }

    // Remove a row from a sheed by id
    public static function remove(string $sheet, int $id)
    {
        // Check if id is equal with 1 and return
        if ($id === 1) {
            return false;
        }

        // Get the range of the row for deleting all the data
        $range = 'A' . $id . ':' . range('A', 'Z')[13] . $id;


        // Remove the data using update method
        $update = (new self())->sheet->sheet($sheet)->range($range)->update([["", "", "", "", "", "", "", "", "", "", "", "", ""]]);

        // If is not updating is returnig false
        if (!$update) {
            return false;
        }

        // Return the row
        return true;
    }

    // Update a row from a sheet using id
    public static function edit(string $sheet, int $id, array $row)
    {

        // Check if id is equal with 1 and return
        if ($id === 1) {
            $id += 1;
        }

        // Get the range of the row for deleting all the data
        $range = 'A' . $id . ':' . range('A', 'Z')[14] . $id;

        // Remove the data using update method
        $update = (new self())->sheet->sheet($sheet)->range($range)->update([$row]);

        // If is not updating is returnig false
        if (!$update) {
            return false;
        }

        // Return the row
        return true;
    }

    // Return a array row from a specific sheet using only the id
    public static function getSheetRowById(string $sheet, int $id)
    {
        // Get the rows of a specific sheet
        $rows = self::get($sheet);

        // Return the result
        $result = array_values($rows)[0];
        return $result;
    }

    // Return a row from a specific sheet using only a value if its fits with a row it will return it
    public static function getSheetRowByValue(string $sheet, $value)
    {
        // Check if is value else return false
        if (!$value) {
            return false;
        }

        // Get all the rows of a sheet
        $rows = self::get($sheet);

        // Check if is any of the array the value given in the argument $value
        $result = array_filter($rows, function ($rows) use ($value) {
            return in_array($value, $rows);
        });

        // Check if result is not empty
        if (!$result) {
            return;
        }

        // Return the result
        $result = array_values($result)[0];
        return $result;
    }

    // Return an array with all the rows where header and returns all the rows equal to value
    public static function getSheetRowWhere(string $sheet, string $header, $value)
    {
        // Check if is value else return false
        if (!$value) {
            return false;
        }

        // Get all the rows of a sheet
        $rows = self::get($sheet);
        // Return all the rows where the values are equal
        $result = array_filter($rows, function ($row) use ($header, $value) {
            return value($row[$header]) == $value;
        });

        // Return the result
        return $result;
    }

    // Get a count of the rows for a specific sheet
    public static function count(string $sheet)
    {
        // Count the rows
        $rows = count((new self())->sheet->sheet($sheet)->get()->all());

        // Return the rows
        return $rows;
    }

    // Get a count of the rows for a specific sheet where the header is equat to valie
    public static function countWhere(string $sheet, string $header, $value)
    {
        // Return an array with all the rows where header and returns all the rows equal to value
        $result = self::getSheetRowWhere($sheet, $header, $value);

        // Return the rows
        return count($result);
    }

    // Get the headers of a specific sheet
    public static function header(string $sheet)
    {
        // Get the specific sheet
        $sheet = (new self())->sheet->sheet($sheet)->get();

        // Pull out an save the headers into a variable
        $header = $sheet->pull(0);

        // Return the headers of the sheet
        return $header;
    }
}
