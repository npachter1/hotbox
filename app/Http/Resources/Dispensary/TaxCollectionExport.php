<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Tax;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class TaxCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('tax_schema');
        $this->headers = [
                'name'          => 'Tax Group Name',
                'location.name' => 'Location',
                'nature_type'   =>  'Product Type',
                'rates_count'   => 'Rates (Count)'

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

        $query = Tax::query()
            ->with('location')
            ->withCount('rates')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}