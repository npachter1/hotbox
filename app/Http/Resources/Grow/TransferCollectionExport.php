<?php

namespace App\Http\Resources\Grow;

use App\Models\Grow\Transfer;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class TransferCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('transfer_schema');
        $this->headers = [
                'type'                      => 'Type',
                'manifest_id'               => 'Manifest #',
                'receiver.name'             => 'Receiver Company Name ',
                'receiver.licensenum'       => 'Receiver License #',
                'transporter_name'          => 'Transporter name',
                'transporter_licensenum'    => 'Transporter License #',
                'manifest_data.status'      => 'Manifest Status',
                'packages_count'            => '# Packages',
                'created_at'                => 'Created On',
                'received_at'               => 'Received On'
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

        $query = Transfer::query()
            ->with('location','receiver')
            ->withCount('packages')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}