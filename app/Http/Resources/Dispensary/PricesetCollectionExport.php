<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Priceset;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class PricesetCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('priceset_schema');
        $this->headers = [
                'name_grade'        => 'Name/Grade',
                'categories_count'    => '#Categories',
                'inventory_count'    => '#Items',
                'rank'              => 'Rank',
                'is_active'         => 'Is Active?',
                'type_uom'          => 'Pricing Type',
                'amount_tiers'      => 'Tiers'
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
    
    
    public static function _renderPricing($data){
        
        return 'table'; // TODO
        
        
    }
    
    
    
    /* prepare query from filters passed */
    public function query(){
        
        $filters = data_get($this->data,'filter',[]);

        $query = Priceset::query()
            ->withCount('categories','inventory')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}