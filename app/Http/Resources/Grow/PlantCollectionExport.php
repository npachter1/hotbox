<?php

namespace App\Http\Resources\Grow;

use App\Models\Grow\Plant;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class PlantCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('plant_schema');
        $this->headers = [
                'label'             => 'Label',
                'growth_phase'              => 'Growth Phase',
                'strain.name'      => 'Strain',
                'vegetative_at' => 'Veg Date',
                'flowering_at'            => 'Flowering Date',
                'plant_batch.name'  => 'Plant Batch'

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

        $query = Plant::query()
            ->with('location')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}