<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UserImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    
    public function model(array $row)
    {
        if (empty($row['name']) || (empty($row['email']) && empty($row['username']))) {
            return null;
        }
        
        $arrayTim = null;
        if (!empty($row['tim'])) {
            $arrayTim = array_map('trim', explode(',', $row['tim']));
        }

        $arrayRole = null;
        if (!empty($row['role'])) {
            $arrayRole = array_map('trim', explode(',', $row['role']));
        }
        return new User([
            'name'      => $row['name'],
            'email'     => $row['email']??null,
            'username'  => $row['username']??null,
            'password'  => $row['password'],
            'role'      => $arrayRole,
            'opd_id'    => $row['opd_id']??null,
            'no_hp'     => $row['no_hp']??null,
            'tim'       => $arrayTim,
        ]);
    }
}
