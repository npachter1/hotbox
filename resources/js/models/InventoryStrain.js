import Model from './Model'

export default class InventoryStrain extends Model {
    
    resource(){
        return 'admin/dispensary/inventoriesstrains';
    }
  
    primaryKey(){
        return 'id';
    }
    
}