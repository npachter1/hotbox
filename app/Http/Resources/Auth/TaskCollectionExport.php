<?php

namespace App\Http\Resources\Auth;

use App\Models\Auth\Task;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class TaskCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('Task_schema');
        $this->headers = [
                'id'          => 'Id',
                'name'      => 'Name',
                'location.name'      => 'Location',
                'due_date'          => 'Due Date',
                'created_by.name'        => 'Created By',
                'created_at'       => 'Date Created',
                'status'          => 'Status',
                'priority_name'         => 'Priority',
                'assignees.name'         => 'Assignees'
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
        $range = [data_get($this->data, 'from', null),data_get($this->data, 'to', null)];

        $query = Task::query()
            ->ofListFilters($filters);

        return $query;
        
    }
    
}