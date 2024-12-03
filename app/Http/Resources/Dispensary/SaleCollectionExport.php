<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Sale;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class SaleCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('sale_schema');
        $this->headers = [
                'location.name'         => 'Location',
                'customer.first_name'   => 'Customer',
                'name'             => 'Drawer ID',
                'user.name'             => 'Budtender',
                'items_count'           => '# SKUs',
                'payments.count'        => '# Transactions',
                'price'                 => 'Price',
                'discount'              => 'Discount Amount',
                'tax'                   => 'Tax Amount',
                'sale_price'            => 'Sale Amount',
                'thc_equivalent_grams'  => 'THC Equivalent Grams',
                'discount_ref'          => 'Discount Codes',
                'status'                => 'Curreent Status',
                'created_at'            => 'Created On'
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

        $query = Sale::query()
            ->with('location','customer','user','drawer')
            ->withCount('items','payments')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}