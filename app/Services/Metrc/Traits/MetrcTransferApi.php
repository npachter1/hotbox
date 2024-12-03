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

trait MetrcTransferApi
{
    
    /**
     * Return incoming transfers to a licensed facility
     *
     * @param $user
     * @param $start (optional)
     * @param $end (optonal)
     *
     * Example response:
     * [
     *  {
     *   "Id": 1,
     *   "ManifestNumber": "0000000001",
     *   "ShipperFacilityLicenseNumber": "123-ABC",
     *   "ShipperFacilityName": "Lofty Med-Cultivation B",
     *   "Name": null,
     *   "TransporterFacilityLicenseNumber": "123-ABC",
     *   "TransporterFacilityName": "Lofty Med-Dispensary",
     *   "DriverName": "X",
     *   "DriverOccupationalLicenseNumber": "",
     *   "DriverVehicleLicenseNumber": "",
     *   "VehicleMake": "X",
     *   "VehicleModel": "X",
     *   "VehicleLicensePlateNumber": "X",
     *   "DeliveryCount": 0,
     *   "ReceivedDeliveryCount": 0,
     *   "PackageCount": 7,
     *   "ReceivedPackageCount": 0,
     *   "ContainsPlantPackage": false,
     *   "ContainsProductPackage": false,
     *   "ContainsTestingSample": false,
     *   "ContainsProductRequiresRemediation": false,
     *   "ContainsRemediatedProductPackage": false,
     *   "CreatedDateTime": "2016-10-10T08:20:45-06:00",
     *   "CreatedByUserName": null,
     *   "LastModified": "0001-01-01T00:00:00+00:00",
     *   "DeliveryId": 1,
     *   "RecipientFacilityLicenseNumber": "123-ABC",
     *   "RecipientFacilityName": "Lofty Med-Cultivation A",
     *   "ShipmentTypeName": "Transfer",
     *   "ShipmentTransactionType": "Standard",
     *   "EstimatedDepartureDateTime": "2016-10-11T14:48:30.000",
     *   "ActualDepartureDateTime": null,
     *   "EstimatedArrivalDateTime": "2016-10-11T16:50:00.000",
     *   "ActualArrivalDateTime": null,
     *   "DeliveryPackageCount": 7,
     *   "DeliveryReceivedPackageCount": 0,
     *   "ReceivedDateTime": "2016-10-11T16:42:19-06:00"
     *  }
     * ]
     */
     
    public function getIncomingTransfers($user,$start=null,$end=null){
       
        $result = $this->get($user, "/transfers/v1/incoming",[
                'lastModifiedStart' => $this->getMetrcDateTimeString($start ?: Carbon::createFromTimestamp(time()-86399)),
                'lastModifiedEnd'   => $this->getMetrcDateTimeString($end ?: Carbon::createFromTimestamp(time()))   // can only fetch 24hs at a time
            ]);

        if ($result->http_code == 200) {
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        }
    }
    
    
    /**
     * Return transfers leaving a licensed facility
     *
     * @param $user
     * @param $start (optional)
     * @param $end (optional)
     *
     *
     */
    public function getOutgoingTransfers($user,$start=null,$end=null){
        
        $result = $this->get($user, "/transfers/v1/outgoing",[
                'lastModifiedStart' => $this->getMetrcDateTimeString($start ?: Carbon::createFromTimestamp(time()-86399)),
                'lastModifiedEnd'   => $this->getMetrcDateTimeString($end ?: Carbon::createFromTimestamp(time()))   // can only fetch 24hs at a time
            ]);     
        
        if ($result->http_code == 200) {
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        }

    }
    
    
    /**
     * Return transfers that have been rejected
     *
     * @param $user
     * @param $start (optional)
     * @param $end (optional)
     *
     *
     */
    public function getRejectedTransfers($user,$start=null,$end=null){
        
        $result = $this->get($user, "/transfers/v1/rejected",[
                'lastModifiedStart' => $this->getMetrcDateTimeString($start ?: Carbon::createFromTimestamp(time()-86399)),
                'lastModifiedEnd'   => $this->getMetrcDateTimeString($end ?: Carbon::createFromTimestamp(time()))   // can only fetch 24hs at a time
            ]);             
        
        if ($result->http_code == 200) {
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        }

    }
    
    
    
    /**
     * Return delivery information by $id
     *
     * @param $id
     *
     * Example response:
     * [
     *  {
     *   "Id": 1,
     *   "RecipientFacilityLicenseNumber": "123-ABC",
     *   "RecipientFacilityName": "Lofty Med-Cultivation A",
     *   "ShipmentTypeName": "Transfer",
     *   "ShipmentTransactionType": "Standard",
     *   "EstimatedDepartureDateTime": "2016-10-11T14:48:30.000",
     *   "ActualDepartureDateTime": null,
     *   "EstimatedArrivalDateTime": "2016-10-11T16:50:00.000",
     *   "ActualArrivalDateTime": null,
     *   "PlannedRoute": "I will use this route.",
     *   "DeliveryPackageCount": 7,
     *   "DeliveryReceivedPackageCount": 0,
     *   "ReceivedDateTime": "2016-10-11T16:42:19-06:00"
     *  }
     * ]
     */
    public function getDeliveries($user,$id){

        $result = $this->get($user,"/transfers/v1/{$id}/deliveries");
            
        if ($result->http_code == 200) {
            return $result->data;
        }else{
                //TODO we got an invalid response or an error on their side
        }

    }
    
    
    /**
     * Return packages in a delivery by delivery $id
     *
     * @param $id
     *
     * Example response:
     * [
     *  {
     *   "PackageId": 1,
     *   "PackageLabel": "ABCDEF012345670000010026",
     *   "PackageType": 0,
     *   "SourceHarvestNames": null,
     *   "ProductName": "Buds",
     *   "ProductCategoryName": null,
     *   "LabTestingState": null,
     *   "IsTestingSample": false,
     *   "ProductRequiresRemediation": false,
     *   "ContainsRemediatedProduct": false,
     *   "RemediationDate": null,
     *   "ShipmentPackageState": "Accepted",
     *   "ShippedQuantity": 10.0,
     *   "ShippedUnitOfMeasureName": "Ounces",
     *   "GrossUnitOfWeightName": null,
     *   "ReceivedQuantity": 10.0,
     *   "ReceivedUnitOfMeasureName": "Ounces"
     *  },
     * ]
     */
    public function getDeliveryPackages($user,$id){

            $result = $this->get($user,"/transfers/v1/delivery/{$id}/packages");

            if ($result->http_code == 200) {
                return $result->data;
            }else{
                //TODO we got an invalid response or an error on their side
            }

    }
    
    
    /**
     * Return all possible states of a transfer
     *
     * Example response:
     * [
     *   "Shipped",
     *   "Rejected",
     *   "Accepted",
     *   "Returned"
     * ]
     */
    public function getTransferPackageStates($user){

        $result = $this->get($user,"/transfers/v1/delivery/package/states");
            
        if ($result->http_code == 200) {
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        }
        
    }
    
    
    /**
     * Return all templates for transfers for a licensed facility
     *
     * @param $licenseNumber
     *
     * Example response:
     * [
     *  {
     *   "Id": 1,
     *   "ManifestNumber": "0000000001",
     *   "ShipperFacilityLicenseNumber": "123-ABC",
     *   "ShipperFacilityName": "Lofty Med-Cultivation B",
     *   "Name": null,
     *   "TransporterFacilityLicenseNumber": "123-ABC",
     *   "TransporterFacilityName": "Lofty Med-Dispensary",
     *   "DriverName": "X",
     *   "DriverOccupationalLicenseNumber": "",
     *   "DriverVehicleLicenseNumber": "",
     *   "VehicleMake": "X",
     *   "VehicleModel": "X",
     *   "VehicleLicensePlateNumber": "X",
     *   "DeliveryCount": 0,
     *   "ReceivedDeliveryCount": 0,
     *   "PackageCount": 7,
     *   "ReceivedPackageCount": 0,
     *   "ContainsPlantPackage": false,
     *   "ContainsProductPackage": false,
     *   "ContainsTestingSample": false,
     *   "ContainsProductRequiresRemediation": false,
     *   "ContainsRemediatedProductPackage": false,
     *   "CreatedDateTime": "2016-10-10T08:20:45-06:00",
     *   "CreatedByUserName": null,
     *   "LastModified": "0001-01-01T00:00:00+00:00",
     *   "DeliveryId": 1,
     *   "RecipientFacilityLicenseNumber": "123-ABC",
     *   "RecipientFacilityName": "Lofty Med-Cultivation A",
     *   "ShipmentTypeName": "Transfer",
     *   "ShipmentTransactionType": "Standard",
     *   "EstimatedDepartureDateTime": "2016-10-11T14:48:30.000",
     *   "ActualDepartureDateTime": null,
     *   "EstimatedArrivalDateTime": "2016-10-11T16:50:00.000",
     *   "ActualArrivalDateTime": null,
     *   "DeliveryPackageCount": 7,
     *   "DeliveryReceivedPackageCount": 0,
     *   "ReceivedDateTime": "2016-10-11T16:42:19-06:00"
     *  }
     * ]
     */
    public function getTransferTemplates($user){

        $result = $this->get($user,"/transfers/v1/templates");
            
        if ($result->http_code == 200) {
            return $result->data;
        }else{
            // TODO we got an invalid response or an error on their side
        }

    }
    
    
}
    
