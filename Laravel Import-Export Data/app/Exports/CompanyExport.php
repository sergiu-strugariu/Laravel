<?php

namespace App\Exports;

use App\Models\Company;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CompanyExport implements FromCollection, WithMapping , WithHeadings
{
    
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return Company::all();
    }

    /**
    * @var Company $company
    */

    public function map($company): array
    {
        return [
            $company->name,
            $company->email,
            $company->phone,
            $company->website,
            $company->employes,
            $company->remote_employes !== 0 ? $company->remote_employes : "0",
            $company->status !== 0 ? $company->status : "0",
        ];
    }
    
    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'website',
            'employes',
            'remote_employes',
            'status'
        ];
    }
}