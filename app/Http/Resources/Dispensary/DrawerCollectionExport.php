<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Drawer;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class DrawerCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('drawer_schema');
        $this->headers = [
            'location.name'         => 'Location',
            'user.name'             => 'Budtender',
            'opening_balance'       => 'Opening',
            'current_balance'       => 'Current',
            'closing_balance'       => 'Closing',
            'sales_count'           => 'Total Sales',
            'events_count'          => 'Total Events',
            'total_sale_price'      => 'Total Sale Price',
            'closed_at'             => 'Closed On',
            'created_at'            => 'Created On',
            'updated_at'            => 'Last Updated On'

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

        $query = Drawer::query()
            ->with('location','user')
            ->withCount('sales','events')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}