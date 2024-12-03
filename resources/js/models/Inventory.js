import Model from './Model'

export default class Inventory extends Model {
    
    resource(){
        return 'admin/dispensary/inventories';
    }
  
    primaryKey(){
        return 'id';
    }
    
}