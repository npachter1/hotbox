<?php

namespace App\Http\Resources\Auth;

use App\Models\Auth\User;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class UserCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('addressbook_schema');
        $this->headers = [
                'name'                          => 'UserName',
                'email'                         => 'Email',
                'status'                        => 'Account Status',
                'type'                          => 'Staff Type',
                'settings.employee_licensenum'  => 'Employee License',
                'settings.employee_hired_on'    => 'Employee Hired On',
                'settings.contact_phone'        => 'Contact Phone',
                'settings.contact_email'        => 'Contact Email',
                'created_at'                    => 'Created On',
                'updated_at'                    => 'Updated On'
            ];
    }
    
    
    public function headings(): array{
        return array_values($this->headers);
    }
    
    
    public function map($model): array{
        
        $data = [];
        foreach(array_keys($this->headers) as $field)
            $data[] = data_get($model,$field,null);
        
        
        return $data;
        
    }


    public function getCsvSettings(): array{
        return [
            'input_encoding' => 'ISO-8859-1'
        ];
    }
    
    
    /* prepare query from filters passed */
    public function query(){
        
        $filters = data_get($this->data,'filter',[]);

        $query = User::query()
            ->ofListFilters($filters)
            ->ofLocation();


        return $query;
        
    }
    
    
}