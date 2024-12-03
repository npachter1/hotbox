<?php

namespace App\Http\Resources\Grow;

use App\Models\Grow\Strain;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class StrainCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('strain_schema');
        $this->headers = [
                'name'             => 'Name',
                'testing_status'              => 'Testing Status',
                'thc_level'      => 'Total THC Level',
                'cbd_level'            => 'Total CBD Level',
                'indica_percentage'             => 'Indica %',
                'sativa_percentage'        => 'Sativa %'
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

        $query = Strain::query()
            ->with('location')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}