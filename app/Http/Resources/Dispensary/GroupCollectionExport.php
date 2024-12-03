<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Group;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class GroupCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('group_schema');
        $this->headers = [
                'name'  => 'Name',
                'type' => 'Assignment Type',
                'customers_count' => 'Customers Assigned',
                'campaigns_count'   => 'Campaigns Assigned',
                'last_synced_at'        => 'Last Synced At',
                'updated_at'            => 'Last Updated At',
                'created At'            => 'Created At'

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

        $query = Group::query()
            ->withCount('customers','campaigns')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}