<?php

namespace App\Services\Metrc\Traits;

use App\Models\Auth\Suite;
use App\Models\AppSchema;

use Auth;
use Log;
use stdClass;
use Exception;
use Carbon\Carbon;
use Storage;

trait MetrcSaleApi
{
    
    /**
     * Return all types of customers
     *
     * Example response:
     * [
     *   "Consumer",
     *   "Patient",
     *   "Caregiver",
     *   "ExternalPatient"
     * ]
     */
    public function getCustomerTypes($user) {
        $result = $this->get($user, "/sales/v1/customertypes");
           
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return all receipts for a licensed facility
     *
     * @param $licenseNumber
     *
     * Example response:
     * [
     *  {
     *   "Id": 1,
     *   "ReceiptNumber": null,
     *   "SalesDateTime": "2016-01-01T17:35:45.000",
     *   "SalesCustomerType": "Consumer",
     *   "PatientLicenseNumber": null,
     *   "CaregiverLicenseNumber": null,
     *   "IdentificationMethod": null,
     *   "TotalPackages": 0,
     *   "TotalPrice": 0.0,
     *   "Transactions": [
     *     {
     *      "PackageId": 71,
     *      "PackageLabel": "ABCDEF012345670000010331",
     *      "ProductName": "Shake",
     *      "QuantitySold": 1.0,
     *      "UnitOfMeasureName": "Ounces",
     *      "UnitOfMeasureAbbreviation": "oz",
     *      "TotalPrice": 9.99,
     *      "SalesDeliveryState": null,
     *      "ArchivedDate": null,
     *      "LastModified": "0001-01-01T00:00:00+00:00"
     *     }
     *   ],
     *   "ArchivedDate": null,
     *   "LastModified": "0001-01-01T00:00:00+00:00"
     *  },
     * ]
     */
    public function getReceipts($user) {
        $result = $this->get($user, "/sales/v1/receipts");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Returns a receipt by $id
     *
     * @param $id
     *
     * Returns an array of receipts. See getReceipts($licenseNumber) for example of data returned for a single receipt
     */
    public function getReceiptsById($user,$id) {
        $result = $this->get($user, "/sales/v1/receipts/{$id}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Create a new receipt
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "SalesDateTime": "2016-10-04T16:44:53.000",
     *   "SalesCustomerType": "Consumer",
     *   "PatientLicenseNumber": null,
     *   "CaregiverLicenseNumber": null,
     *   "IdentificationMethod": null,
     *   "Transactions": [
     *    {
     *     "PackageLabel": "ABCDEF012345670000010331",
     *     "Quantity": 1.0,
     *     "UnitOfMeasure": "Ounces",
     *     "TotalAmount": 9.99
     *    },
     *   ]
     *  },
     * ]
     */
    public function postReceipts($user,$data) {
        $result = $this->post($user, "/sales/v1/receipts",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Edit a sales receipt
     *
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "Id": 51,
     *   "SalesDateTime": "2016-10-04T16:47:24.000",
     *   "SalesCustomerType": "Consumer",
     *   "PatientLicenseNumber": null,
     *   "CaregiverLicenseNumber": null,
     *   "IdentificationMethod": null,
     *   "Transactions": [
     *     {
     *      "PackageLabel": "ABCDEF012345670000010331",
     *      "Quantity": 1.0,
     *      "UnitOfMeasure": "Ounces",
     *      "TotalAmount": 9.98
     *     },
     *    ]
     *  },
     * ]
     */
    public function putReceipts($user,$data) {
        $result = $this->put("/sales/v1/receipts",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Delete a receipt by $id
     *
     * @param $id
     */
    public function deleteReceipts($user, $id) {
        $result = $this->delete($user, "/sales/v1/receipts/{$id}");
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Return transaction data for a licensed facility
     *
     * @param $licenseNumber
     *
     * Example response:
     * [
     *  {
     *   "SalesDate": "2015-01-08",
     *   "TotalTransactions": 40,
     *   "TotalPackages": 40,
     *   "TotalPrice": 399.6
     *  },
     * ]
     */
    public function getTransactions($user) {
        $result = $this->get($user, "/sales/v1/transactions");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Return transactions by date
     *
     * @param $date
     * @param $licenseNumber
     *
     * Example response:
     * [
     *  {
     *   "PackageId": 71,
     *   "PackageLabel": "ABCDEF012345670000010331",
     *   "ProductName": "Shake",
     *   "QuantitySold": 1.0,
     *   "UnitOfMeasureName": "Ounces",
     *   "UnitOfMeasureAbbreviation": "oz",
     *   "TotalPrice": 9.99,
     *   "SalesDeliveryState": null,
     *   "ArchivedDate": null,
     *   "LastModified": "0001-01-01T00:00:00+00:00"
     *  },
     * ]
     */
    public function getTransactionsByDate($user,$date) {
        $result = $this->get($user, "/sales/v1/transactions/{$date}");
            
        if ($result->http_code == 200) {
            return $result->data;
        }
        else {
            //we got an invalid response or an error on their side
        }
    }
    
    /**
     * Create transactions
     *
     * @param $date
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "PackageLabel": "ABCDEF012345670000010331",
     *   "Quantity": 1.0,
     *   "UnitOfMeasure": "Ounces",
     *   "TotalAmount": 9.99
     *  },
     * ]
     */
    public function postTransactions($user,$date,$data) {
        $result = $this->post($user, "/sales/v1/transactions/{$date}",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
    
    /**
     * Edit transactions
     *
     * @param $date
     * @param $licenseNumber
     * @param $data
     *
     * Example request:
     * [
     *  {
     *   "PackageLabel": "ABCDEF012345670000010331",
     *   "Quantity": 1.0,
     *   "UnitOfMeasure": "Ounces",
     *   "TotalAmount": 9.99
     *  },
     * ]
     */
    public function putTransactions($user,$date,$data) {
        $result = $this->post($user, "/sales/v1/transactions/{$date}",$data);
            
        // Metrc doesn't return a response, just an HTTP code
        return $result->http_code;
    }
}
