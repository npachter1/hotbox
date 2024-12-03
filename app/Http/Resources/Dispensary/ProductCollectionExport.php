<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Product;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class ProductCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('product_schema');
        $this->headers = [
                'name' => 'Product Name',
                'sku' => 'Product SKU',
                'description' => 'Description',
                'category.name' => 'Category name',
                'nature_type' => 'Nature Type',
                
                'inv_meta.received'  => '(Last Received) Date',
                'inv_meta.uom'       => '(Last Received) Unit of Measure',      // first inventory record is latest received
                'inventory.0.priceset.name_grade'  => '(Last Received) Priceset',             // first inventory record is latest received
                'inv_meta.cost'      => '(Last Received) Unit Cost',
                'inv_meta.expiring'  => '(Last Received) Expires On',
                'inv_meta.revenue'   => 'Revenue To Date',
                'inventory.0.vendor.name'    => '(Last Received) Vendor',
                'inv_meta.strain'    => '(Last Received) Metrc Strain',
                'inv_meta.received_at' => '(Last Received) Date',

                'category.metrc_category_type' => 'Metrc/Type',
                'archived_at'   => 'Archived on',
                'created_at'   => 'Created on',
                'updated_at'   => 'Updated on',
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

        $query = Product::query()
            ->with('category.type','inventory.sales.sale','inventory.pricing')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}