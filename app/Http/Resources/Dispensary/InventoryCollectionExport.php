<?php

namespace App\Http\Resources\Dispensary;

use App\Models\Dispensary\Inventory;
use App\Models\AppSchema;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;


class InventoryCollectionExport implements FromQuery, WithHeadings, WithCustomCsvSettings, WithMapping
{
    
    use Exportable;
    public function __construct(array $data){
        $this->data = $data;
        $this->schema = AppSchema::getSchema('inventory_schema');
        $this->headers = [
                'item_barcode'      => 'Barcode ID',
                'metrc_tag'         => 'Metrc tag',
                'item_name'         => 'Product Name',
                'product.category.name' => 'Category Name',
                'product.nature_type' => 'Nature Type',
                'taxes.name'          => 'Assigned Tax Group',
                'priceset.name'     => 'Assigned Pricing Set',
                'addressbook.name'  => 'Vendor Name',
                'receiving.po_number' => 'PurchaseOrder ref',
                'item_batch'        => 'Batch notes',
                'item_notes'        => 'Notes',
                'amount_unit'       => 'Amount per Unit (grams)',
                'weight_potency'    => 'Weight/Potency',
                'cost_unit'         => 'Per Unit Cost',
                'expires_at'        => 'Expires At',
                'created_at'        => 'Received At',
                'audited_at'        => 'Last Audited At',
                'auditor.name'      => 'Last Audited By',
                'quantity_received' => 'Qty Received',
                'quantity_sold'     => 'Qty Sold',
                'quantity_adjust'   => 'Qty Adjusted',
                'quantity_pending'  => 'Qty Pending',
                'quantity_on_hand'  => 'Qty On Hand'
            ];
    }
    
    public function map($model): array{
        
        $data = [];
        foreach(array_keys($this->headers) as $field)
            $data[] = data_get($model,$field,null);
        
        
        return $data;
        
    }
    
    
    public function headings(): array{
        return array_values($this->headers);
    }
    
    
    public function getCsvSettings(): array{
        return [
            'input_encoding' => 'ISO-8859-1'
        ];
    }
    
    
    /* prepare query from filters passed */
    public function query(){
        
        $filters = data_get($this->data,'filter',[]);

        $query = Inventory::query()
            ->with('product.category','taxes','receiving','vendor','auditor')
            ->ofListFilters($filters)
            ->ofActive();

        return $query;
        
    }
    
}