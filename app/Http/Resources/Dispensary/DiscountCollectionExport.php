<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Discount;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class DiscountCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('discount_schema');
        $this->headers = [
                'name'          => 'Internal Name',
                'descriptor'    => 'Receipts Descriptor',
                'type'          => 'Discount Rule Type',
                'discount_type' => 'Discount Type',
                'discount_amount'          => 'Amount',
                'discount_code'          => 'Code',
                'distributrion_type'          => 'Distribution Type',
                'settings.pointsToRedeem'          => 'Points needed (to redeem)',
                'settings.minSales'          => 'Min Sales Count',
                'rank'          => '',
                'is_exclusive'          => '',
                'max_per_customer'          => '',
                'is_active'          => '',
                'scheduled_at'          => '',
                'created_at'          => '',
                'updated_at'          => ''

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

        $query = Discount::query()
            
            ->ofListFilters($filters);


        return $query;
        
    }
    
}