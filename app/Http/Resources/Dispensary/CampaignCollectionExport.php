<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Campaign;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class CampaignCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('campaign_schema');
        $this->headers = [
                'name' => 'Reference Name for this Campaign',
                'type' => 'Delivery Type',
                'status' => 'Campaign Current Status',
                'campaign_code' => '[discount] Campaign Code',
                'subject' => 'Notification Subject',
                'message' => 'Message',
                'scheduled_at' => 'Sending Start At',
                'customer_log_count' => 'Customers in Campaign',
                'created_at'        => 'Created on'

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

        $query = Campaign::query()
            
            ->ofListFilters($filters);


        return $query;
        
    }
    
}