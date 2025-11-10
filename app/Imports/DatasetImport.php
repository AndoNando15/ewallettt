<?php

namespace App\Imports;

use App\Models\Dataset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DatasetImport implements ToModel, WithHeadingRow
{
    /**
     * Convert each row to a model.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Normalize the keys in case the column names have different cases or spaces
        $row = array_map('trim', $row);  // Trim spaces from the column names
        $row = array_change_key_case($row, CASE_UPPER);  // Make all keys uppercase for matching

        return new Dataset([
            'nama_platform_e_wallet' => $row['NAMA_PLATFORM_E_WALLET'],
            'VTP' => $row['VTP'],
            'NTP' => $row['NTP'],
            'PPE' => $row['PPE'],
            'FPE' => $row['FPE'],
            'PSD' => $row['PSD'],
            'IPE' => $row['IPE'],
            'PKP' => $row['PKP'],
        ]);
    }
}