<?php

namespace App\Http\Resources\Auth;

use App\Models\Auth\Addressbook;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class AddressbookCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('addressbook_schema');
        $this->headers = [
                'name'          => 'Recipient',
                'address1'      => 'Address Line 1',
                'address2'      => 'Address Line 2',
                'city'          => 'City',
                'region'        => 'State/Prov',
                'country'       => 'Country',
                'city'          => 'City',
                'phone'         => 'Phone',
                'email'         => 'Email',
                'contact_notes' => 'Notes from Contact',
                'type'          => 'Category',
                'created_at'    => 'Created On',
                'updated_at'    => 'Updated On'
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
        $range = [data_get($this->data, 'from', null),data_get($this->data, 'to', null)];

        $query = Addressbook::query()
            ->ofListFilters($filters);

        return $query;
        
    }
    
}