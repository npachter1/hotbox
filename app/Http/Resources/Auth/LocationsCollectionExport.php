<?php

namespace App\Http\Resources\Auth;

use App\Models\Auth\User;
use App\Models\Auth\Location;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class LocationsCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('location_schema');
        $this->headers = [
                'id'            => 'Location ID',
                'name'          => 'Location Name',
                'status'        => 'Location Status',
                'is_demo'       => 'Demo/Test Mode',
                'settings.contact_email'    => 'Contact Email',
                'activated_at'  => 'Activated On',
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

        $query = Location::query()
            ->ofListFilters($filters);


        return $query;
        
    }
    
    
}