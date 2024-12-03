<?php

namespace App\Http\Resources\Auth;

use App\Models\Auth\Servicelog;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class ServicelogCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('servicelog_schema');
        $this->headers = [
                'status' => 'Status',
                'type' => 'Type',
                'name' => 'Name/EP Reference',
                'entity_ref' => 'Article/Resource Ref #',
                'request_ref' => 'Request Ref/ID',
                'response_ref' => 'Response ref/error mes',

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

        $query = Servicelog::query()
            ->ofListFilters($filters);


        return $query;
        
    }
    
}