<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Customer;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class CustomerCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('customer_schema');
        $this->headers = [
                'type'          => 'Type',
                'location.name' => 'Location',
                'first_name'    => 'First Name',
                'middle_name'   => 'Middle Name',
                'last_name'     => 'Last Name',
                'address.city'  => 'City',
                'address.region'=> 'State',
                'address.email' => 'Email',
                'address.phone' => 'Phone',
                'birthdate'     => 'Birthday',
                'drivers_license_state' => 'Drivers License State',
                'drivers_license_expiry_date' => 'Drivers License Expiration Date',
                'mmj_card_state' => 'MMJ Card License State',
                'mmj_card_expiry_date' => 'MMJ Card Expiration Date',
                'email_optin'   => 'Email Optin',
                'sms_optin'     => 'SMS Optin',
                'referral_count'        => 'Referral Count',
                'total_reward_points'   => 'Total Reward Points',
                'total_spent'           => 'Total Spent',
                'settings.med_carry_weight'     => 'Med carry Weight',
                'settings.med_plant_count'      => 'Med Plant Count'
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

        $query = Customer::query()
            ->with('location','address')
            ->ofListFilters($filters);


        return $query;
        
    }
    
}