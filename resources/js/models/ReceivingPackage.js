import Model from './Model'

export default class ReceivingPackage extends Model {
    
    resource(){
        return 'admin/dispensary/receivings/packages';
    }
  
    primaryKey(){
        return 'id';
    }
    
}