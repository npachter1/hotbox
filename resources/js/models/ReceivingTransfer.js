import Model from './Model'

export default class ReceivingTransfer extends Model {
    
    resource(){
        return 'admin/dispensary/receivings/transfers';
    }
  
    primaryKey(){
        return 'id';
    }
    
}