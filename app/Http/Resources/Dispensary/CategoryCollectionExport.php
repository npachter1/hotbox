<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Category;
use App\Models\Dispensary\CategoryMetrc;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class CategoryCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('category_schema');
        $this->headers = [
                'name'                  => 'Category Title',
                'contains_thc'          => 'Contains THC',
                'products_count'        => '# Products',
                'notes'                 => 'Notes',
                'type'                  => 'Type',
                'equivalency_type'      => 'THC Equivalency Type',
                'settings.thc_equiv_ratio'=> 'THC Equiv Ratio',
                'created_at'            => 'Created On',
                'updated_at'            => 'Updated On'
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

        $query = Category::query()
            ->withCount('products')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}